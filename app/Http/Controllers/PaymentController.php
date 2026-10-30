<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.patient:id,name', 'receiver:id,name']);

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest('payment_date')->latest('id')->get();

        $success = Payment::where('status', 'Success');

        return response()->json([
            'success'        => true,
            'payments'       => $payments,
            'totalPayments'  => $payments->count(),
            'totalCollected' => (float) (clone $success)->sum('amount'),
            'monthCollected' => (float) (clone $success)->whereYear('payment_date', now()->year)
                ->whereMonth('payment_date', now()->month)->sum('amount'),
            'pendingCount'   => Payment::where('status', 'Pending')->count(),
            'methods'        => Payment::METHODS,
        ]);
    }

    public function show(string $id)
    {
        $payment = Payment::with(['invoice.patient', 'receiver:id,name'])->find($id);

        if (!$payment) {
            return response()->json(['error' => true, 'message' => 'Payment not found'], 404);
        }

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'methods' => Payment::METHODS,
        ]);
    }

    /** Counter payment against an invoice. Body: invoice_id, amount, payment_method, payment_date */
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id'     => 'required|exists:invoices,id',
            'amount'         => 'required|numeric|min:0.01|max:99999999.99',
            'payment_method' => ['required', Rule::in(Payment::METHODS)],
            'payment_date'   => 'required|date',
        ]);

        $invoice = Invoice::find($data['invoice_id']);

        if ($invoice->status === 'Cancelled') {
            return response()->json([
                'error'   => true,
                'message' => 'Cannot take payment on a cancelled invoice.',
            ], 409);
        }

        $payment = DB::transaction(function () use ($data) {
            $locked = Invoice::lockForUpdate()->findOrFail($data['invoice_id']);
            $due = round((float) $locked->total_amount - (float) $locked->paid_amount, 2);

            if ($data['amount'] > $due + 0.001) {
                throw ValidationException::withMessages([
                    'amount' => 'Amount cannot exceed the due ' . number_format($due, 2) . '.',
                ]);
            }

            $payment = $locked->payments()->create($data + [
                'status'      => 'Success',
                'received_by' => auth()->id(),
            ]);

            $locked->syncPayments();

            return $payment;
        });

        return response()->json([
            'success' => 'Payment received',
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => true, 'message' => 'Payment not found'], 404);
        }

        if ($payment->isGateway()) {
            return response()->json([
                'error'   => true,
                'message' => 'Online (gateway) payments cannot be edited.',
            ], 409);
        }

        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:99999999.99',
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_date'   => 'required|date',
        ]);

        DB::transaction(function () use ($payment, $data) {
            $invoice = Invoice::lockForUpdate()->findOrFail($payment->invoice_id);
            $others = (float) $invoice->payments()->where('status', 'Success')
                ->where('id', '!=', $payment->id)->sum('amount');
            $maxAllowed = round((float) $invoice->total_amount - $others, 2);

            if ($data['amount'] > $maxAllowed + 0.001) {
                throw ValidationException::withMessages([
                    'amount' => 'Amount cannot exceed ' . number_format($maxAllowed, 2) . ' (invoice total minus other payments).',
                ]);
            }

            $payment->update($data);
            $invoice->syncPayments();
        });

        return response()->json(['success' => true, 'message' => 'Payment updated']);
    }

    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => true, 'message' => 'Payment not found'], 404);
        }

        DB::transaction(function () use ($payment) {
            $invoice = Invoice::lockForUpdate()->findOrFail($payment->invoice_id);
            $payment->delete();
            $invoice->syncPayments();
        });

        return response()->json(['success' => true, 'message' => 'Payment deleted and invoice recalculated']);
    }
}
