<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * JSON version of App\Http\Controllers\InvoiceController (the Blade one stays for web.php).
 */
class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['patient:id,name,phone', 'admission:id,status', 'appointment:id,appointment_date'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success'       => true,
            'invoices'      => $invoices,
            'totalInvoices' => $invoices->count(),
            'unpaidCount'   => $invoices->where('status', 'Unpaid')->count(),
            'paidCount'     => $invoices->where('status', 'Paid')->count(),
            'totalDue'      => $invoices->whereIn('status', ['Unpaid', 'Partially Paid'])
                ->sum(fn ($i) => $i->total_amount - $i->paid_amount),
        ]);
    }

    /** Dropdown data for the invoice form. ?invoice_id= keeps the current admission in the list. */
    public function options(Request $request)
    {
        $currentAdmission = $request->filled('invoice_id')
            ? Invoice::where('id', $request->query('invoice_id'))->value('admission_id')
            : null;

        return response()->json([
            'success'      => true,
            'patients'     => Patient::orderBy('name')->get(['id', 'name', 'phone']),
            'admissions'   => Admission::with('patient:id,name')
                ->where('status', 'Admitted')
                ->when($currentAdmission, fn ($q) => $q->orWhere('id', $currentAdmission))
                ->get(['id', 'patient_id', 'admission_date', 'status']),
            'appointments' => Appointment::with('patient:id,name')
                ->orderBy('appointment_date', 'desc')
                ->get(['id', 'patient_id', 'appointment_date']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'admission_id'   => 'nullable|exists:admissions,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'total_amount'   => 'required|numeric|min:0',
            'invoice_date'   => 'required|date',
        ]);

        // Paid amount / status come from Payments, never typed by hand.
        $invoice = Invoice::create($validated + ['paid_amount' => 0, 'status' => 'Unpaid']);

        return response()->json([
            'success' => 'Invoice created successfully',
            'invoice' => $invoice,
        ]);
    }

    public function show(string $id)
    {
        $invoice = Invoice::with([
            'patient', 'admission.ward', 'admission.bed', 'appointment.doctor.user',
            'items', 'payments.receiver:id,name',
        ])->find($id);

        if (!$invoice) {
            return response()->json(['error' => true, 'message' => 'Invoice not found'], 404);
        }

        return response()->json(['success' => true, 'invoice' => $invoice]);
    }

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['error' => true, 'message' => 'Invoice not found'], 404);
        }

        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'admission_id'   => 'nullable|exists:admissions,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            // total can never drop below what has already been paid
            'total_amount'   => 'required|numeric|min:' . max(0, (float) $invoice->paid_amount),
            'status'         => 'nullable|in:Active,Cancelled',
            'invoice_date'   => 'required|date',
        ]);

        $newStatus = $validated['status'] ?? null;
        unset($validated['status']);

        $invoice->fill($validated);

        // Cancel / re-open. Everything else (Unpaid / Partially Paid / Paid) is automatic.
        // Add your own role check here if the API is behind auth (e.g. auth()->user()->role_id == 1).
        if ($newStatus) {
            $invoice->status = $newStatus === 'Cancelled' ? 'Cancelled' : 'Unpaid';
        }

        $invoice->save();

        return response()->json(['success' => true, 'message' => 'Invoice updated successfully']);
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['error' => true, 'message' => 'Invoice not found'], 404);
        }

        if ($invoice->payments()->exists()) {
            return response()->json([
                'error'   => true,
                'message' => 'This invoice has payment records. Delete its payments first.',
            ], 409);
        }

        $invoice->items()->delete();
        $invoice->delete();

        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully']);
    }

    /** POST appointments/{id}/generate-invoice : doctor-fee invoice, no duplicates. */
    public function fromAppointment(string $id)
    {
        $appointment = Appointment::with('doctor.user')->find($id);

        if (!$appointment) {
            return response()->json(['error' => true, 'message' => 'Appointment not found'], 404);
        }

        $invoice = Invoice::where('appointment_id', $appointment->id)->first();

        // The same visit may already be billed inside a prescription / admission invoice
        // (it carries the same doctor's Consultation Fee). Reuse it instead of billing the fee twice.
        if (!$invoice && $appointment->doctor_id) {
            $invoice = Invoice::where('patient_id', $appointment->patient_id)
                ->where('status', '!=', 'Cancelled')
                ->whereNull('appointment_id')
                ->whereDate('invoice_date', Carbon::parse($appointment->appointment_date)->toDateString())
                ->whereHas('items', fn ($q) => $q
                    ->where('item_type', 'Consultation Fee')
                    ->where('item_reference_id', $appointment->doctor_id))
                ->first();

            // link it to this appointment so it is found directly next time
            $invoice?->update(['appointment_id' => $appointment->id]);
        }

        $invoice ??= new Invoice();

        if (!$invoice->exists) {
            $invoice->patient_id     = $appointment->patient_id;
            $invoice->appointment_id = $appointment->id;
            $invoice->invoice_date = $appointment->appointment_date ?? now();
            $invoice->status       = 'Unpaid';
            $invoice->total_amount = 0;
            $invoice->save();
        }

        $fee = $appointment->doctor?->consultation_fee;

        if ($fee && !$invoice->items()->where('item_type', 'Consultation Fee')->exists()) {
            $invoice->items()->create([
                'item_type'         => 'Consultation Fee',
                'item_reference_id' => $appointment->doctor_id,
                'description'       => 'Consultation — Dr. ' . ($appointment->doctor->user->name ?? 'N/A'),
                'amount'            => $fee,
            ]);
        }

        $invoice->update(['total_amount' => $invoice->items()->sum('amount')]);

        return response()->json([
            'success'    => true,
            'message'    => 'Doctor fee invoice is ready to print',
            'invoice_id' => $invoice->id,
        ]);
    }
}