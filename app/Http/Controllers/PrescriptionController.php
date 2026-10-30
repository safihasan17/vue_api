<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Note: Admission ekhono banano hoyni, tai 'admission' relation load kora hocche na.
     * prescriptions.admission_id shobshomoy NULL thakbe.
     */
    public function index()
    {
        $totalPrescriptions = Prescription::count();

        $issuedToday = Prescription::whereDate('prescription_date', today())->count();

        $issuedThisMonth = Prescription::whereMonth('prescription_date', now()->month)
            ->whereYear('prescription_date', now()->year)
            ->count();

        $prescriptions = Prescription::with(['patient:id,name', 'doctor.user:id,name', 'admission:id,status'])
            ->withCount(['prescriptionMedicines', 'labTestOrders'])
            ->latest('prescription_date')
            ->get();

        return response()->json([
            'success'            => true,
            'prescriptions'      => $prescriptions,
            'totalPrescriptions' => $totalPrescriptions,
            'issuedToday'        => $issuedToday,
            'issuedThisMonth'    => $issuedThisMonth,
        ]);
    }

    
    public function options()
    {
        return response()->json([
            'success'      => true,
            'patients'     => Patient::orderBy('name')->get(['id', 'name', 'phone']),
            'doctors'      => Doctor::with(['user:id,name', 'department:id,name'])->get(),
            'appointments' => Appointment::with('patient:id,name')
                ->orderBy('appointment_date', 'desc')
                ->get(['id', 'patient_id', 'doctor_id', 'appointment_date', 'appointment_time']),
            'medicines'    => Medicine::orderBy('name')->get(['id', 'name']),
            'labTests'     => LabTest::orderBy('test_name')->get(['id', 'test_name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePrescription($request);

        $prescription = DB::transaction(function () use ($validated, $request) {
            $data = collect($validated)->except(['medicines', 'lab_tests'])->toArray();

            // Doctor shudhu admission SUGGEST korte pare. Asol admission receptionist
            // pore korbe (admission ready hole prescriptions.admission_id fill hobe).
            $data['admission_advised'] = $request->boolean('admission_advised');

            $prescription = Prescription::create($data);

            $this->syncMedicines($prescription, $validated['medicines'] ?? []);
            $this->syncLabTests($prescription, $validated['lab_tests'] ?? []);

            return $prescription;
        });

        $prescription->load($this->detailRelations());

        return response()->json([
            'success'      => 'Prescription saved successfully',
            'prescription' => $prescription,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prescription = Prescription::with($this->detailRelations())->find($id);

        if ($prescription) {
            return response()->json([
                'success'      => true,
                'prescription' => $prescription,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePrescription($request);

        $prescription = Prescription::find($id);

        if (!$prescription) {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription not found',
            ], 404);
        }

        DB::transaction(function () use ($validated, $request, $prescription) {
            $data = collect($validated)->except(['medicines', 'lab_tests'])->toArray();

            // Receptionist admit korle suggestion "true" thakbe.
            // Ekhon admission_id shobshomoy null, tai request theke-i ashbe.
            $data['admission_advised'] = $prescription->admission_id
                ? true
                : $request->boolean('admission_advised');

            $prescription->update($data);

            // Medicine list ta notun submit kora list diye replace hobe
            $prescription->prescriptionMedicines()->delete();
            $this->syncMedicines($prescription, $validated['medicines'] ?? []);

            // Shudhu Pending lab order replace hoy, baki gulo (Completed etc.) thake
            $prescription->labTestOrders()->where('status', 'Pending')->delete();
            $this->syncLabTests($prescription, $validated['lab_tests'] ?? []);
        });

        $prescription->load($this->detailRelations());

        return response()->json([
            'success'      => true,
            'message'      => 'Prescription updated successfully',
            'prescription' => $prescription,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prescription = Prescription::find($id);

        if ($prescription) {
            $prescription->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prescription deleted successfully',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription not found',
            ], 404);
        }
    }

    /**
     * Prescription theke invoice generate kore.
     * POST /prescriptions/{id}/generate-invoice
     */
    public function generateInvoice(string $id)
    {
        $prescription = Prescription::with(['doctor.user', 'labTestOrders.test', 'medicines'])->find($id);

        if (!$prescription) {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription not found',
            ], 404);
        }

        if ($prescription->admission_id) {
            $invoice = Invoice::firstOrNew(['admission_id' => $prescription->admission_id]);
        } elseif ($prescription->appointment_id) {
            $invoice = Invoice::firstOrNew(['appointment_id' => $prescription->appointment_id]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'This prescription is not linked to an appointment or admission, so an invoice cannot be generated.',
            ], 422);
        }

        $invoice->patient_id   = $prescription->patient_id;
        $invoice->invoice_date = $invoice->invoice_date ?? $prescription->prescription_date;
        $invoice->status       = $invoice->status ?? 'Unpaid';
        $invoice->save();

        $invoice->items()->whereIn('item_type', ['Consultation Fee', 'Lab Test', 'Medicine'])->delete();

        // Admission nei, tai ekhon shobshomoy false. Admission ashle medicine bill-o hobe.
        $isAdmitted = (bool) $prescription->admission_id;

        if ($prescription->doctor && $prescription->doctor->consultation_fee) {
            $invoice->items()->create([
                'item_type'         => 'Consultation Fee',
                'item_reference_id' => $prescription->doctor_id,
                'description'       => 'Consultation — Dr. ' . ($prescription->doctor->user->name ?? 'N/A'),
                'amount'            => $prescription->doctor->consultation_fee,
            ]);
        }

        if ($isAdmitted) {
            foreach ($prescription->medicines as $medicine) {
                $invoice->items()->create([
                    'item_type'         => 'Medicine',
                    'item_reference_id' => $medicine->id,
                    'description'       => $medicine->name,
                    'amount'            => $medicine->unit_price ?? 0,
                ]);
            }
        }

        // Doctor-er order kora lab test shobshomoy bill hoy (Cancelled gulo bad)
        foreach ($prescription->labTestOrders as $order) {
            if ($order->test && $order->status !== 'Cancelled') {
                $invoice->items()->create([
                    'item_type'         => 'Lab Test',
                    'item_reference_id' => $order->test_id,
                    'description'       => $order->test->test_name,
                    'amount'            => $order->test->price ?? 0,
                ]);
            }
        }

        $invoice->update(['total_amount' => $invoice->items()->sum('amount')]);

        return response()->json([
            'success'    => true,
            'message'    => 'Invoice generated from prescription',
            'invoice_id' => $invoice->id,
        ]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function validatePrescription(Request $request): array
    {
        return $request->validate([
            'patient_id'                   => 'required|exists:patients,id',
            'doctor_id'                    => 'required|exists:doctors,id',
            'appointment_id'               => 'nullable|exists:appointments,id',
            'prescription_date'            => 'required|date',
            'notes'                        => 'nullable|string',
            'admission_advised'            => 'nullable|boolean',
            'medicines'                    => 'nullable|array',
            'medicines.*.medicine_id'      => 'required_with:medicines|exists:medicines,id',
            'medicines.*.dosage'           => 'nullable|string|max:100',
            'medicines.*.duration'         => 'nullable|string|max:50',
            'medicines.*.instructions'     => 'nullable|string',
            'lab_tests'                    => 'nullable|array',
            'lab_tests.*.test_id'          => 'required_with:lab_tests|exists:lab_tests,id',
        ]);
    }

    private function syncMedicines(Prescription $prescription, array $rows): void
    {
        foreach ($rows as $row) {
            $prescription->prescriptionMedicines()->create($row);
        }
    }

    private function syncLabTests(Prescription $prescription, array $rows): void
    {
        foreach ($rows as $row) {
            $prescription->labTestOrders()->create([
                'patient_id' => $prescription->patient_id,
                'doctor_id'  => $prescription->doctor_id,
                'test_id'    => $row['test_id'],
                'status'     => 'Pending',
                'order_date' => $prescription->prescription_date,
            ]);
        }
    }

    // show/store/update-er response-e jei relation gulo lage (admission bad)
    private function detailRelations(): array
    {
        return [
            'patient',
            'doctor.user:id,name,email,phone',
            'doctor.department:id,name',
            'appointment',
            'prescriptionMedicines.medicine',
            'labTestOrders.test',
        ];
    }
}
