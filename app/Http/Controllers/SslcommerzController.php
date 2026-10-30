<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\SslcommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SslcommerzController extends Controller
{
    public function __construct(private SslcommerzService $gateway) {}

    /** Receptionist / admin clicks "Pay Online" on an invoice. */
    public function initiate(Request $request, Invoice $invoice)
    {
        if (! $this->gateway->isConfigured()) {
            return back()->with('error', 'SSLCommerz is not configured. Set SSLCOMMERZ_STORE_ID and SSLCOMMERZ_STORE_PASSWORD in .env.');
        }

        if ($invoice->status === 'Cancelled') {
            return back()->with('error', 'Cannot take payment on a cancelled invoice.');
        }

        $due = round((float) $invoice->total_amount - (float) $invoice->paid_amount, 2);

        $data = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . max($due, 1),
        ]);

        if ($due <= 0) {
            return back()->with('error', 'This invoice has no due amount.');
        }

        $invoice->loadMissing('patient');
        $patient = $invoice->patient;
        $tranId = 'INV' . $invoice->id . '-' . strtoupper(Str::random(10));

        $payment = $invoice->payments()->create([
            'amount' => $data['amount'],
            'payment_method' => 'SSLCommerz',
            'payment_date' => now()->toDateString(),
            'gateway' => 'sslcommerz',
            'transaction_id' => $tranId,
            'status' => 'Pending',
             'received_by' => auth::id(),
        ]);

        $response = $this->gateway->createSession([
            'total_amount' => number_format((float) $data['amount'], 2, '.', ''),
            'currency' => config('sslcommerz.currency'),
            'tran_id' => $tranId,
            'success_url' => route('sslcommerz.success'),
            'fail_url' => route('sslcommerz.fail'),
            'cancel_url' => route('sslcommerz.cancel'),
            'ipn_url' => route('sslcommerz.ipn'),
            'cus_name' => $patient->name ?? 'Patient',
            'cus_email' => 'patient@example.com',   // patients table has no email
            'cus_add1' => $patient->address ?: 'N/A',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $patient->phone ?: '01700000000',
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            'product_name' => 'Hospital Bill INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT),
            'product_category' => 'Healthcare',
            'product_profile' => 'general',
            'value_a' => $payment->id,
        ]);

        if (($response['status'] ?? '') === 'SUCCESS' && ! empty($response['GatewayPageURL'])) {
            return redirect()->away($response['GatewayPageURL']);
        }

        $payment->update(['status' => 'Failed']);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('error', 'Could not start online payment: ' . ($response['failedreason'] ?? 'gateway error') . '.');
    }

    /** Browser comes back here after a successful payment (gateway POSTs, no login session needed). */
    public function success(Request $request)
    {
        $payment = $this->findPayment($request);

        if (! $payment) {
            return redirect()->route('dashboard');
        }

        $settled = $this->settle($request, $payment);

        return redirect()->route('invoices.show', [
            'invoice' => $payment->invoice_id,
            'pay' => $settled ? 'success' : 'unverified',
        ]);
    }

    public function fail(Request $request)
    {
        return $this->closePending($request, 'Failed', 'failed');
    }

    public function cancel(Request $request)
    {
        return $this->closePending($request, 'Cancelled', 'cancelled');
    }

    private function closePending(Request $request, string $status, string $flag)
    {
        $payment = $this->findPayment($request);

        if (! $payment) {
            return redirect()->route('dashboard');
        }

        if ($payment->status === 'Pending') {
            $payment->update(['status' => $status]);
        }

        return redirect()->route('invoices.show', ['invoice' => $payment->invoice_id, 'pay' => $flag]);
    }

    /** Server-to-server notification from SSLCommerz (needs a public URL). */
    public function ipn(Request $request)
    {
        $payment = $this->findPayment($request);

        if ($payment) {
            $this->settle($request, $payment);
        }

        return response('OK', 200);
    }

    /* ---------------------------------------------------------------- */

    private function findPayment(Request $request): ?Payment
    {
        $tranId = $request->input('tran_id');

        if (! $tranId) {
            return null;
        }

        return Payment::where('gateway', 'sslcommerz')->where('transaction_id', $tranId)->first();
    }

    /**
     * Confirm with SSLCommerz's own server, then mark Success and update the invoice.
     * Safe to call more than once (success redirect + IPN both arrive).
     */
    private function settle(Request $request, Payment $payment): bool
    {
        if ($payment->status === 'Success') {
            return true;
        }

        $valId = $request->input('val_id');

        if (! $valId) {
            return false;
        }

        $result = $this->gateway->validate($valId);

        $valid = in_array($result['status'] ?? '', ['VALID', 'VALIDATED'], true)
            && ($result['tran_id'] ?? null) === $payment->transaction_id
            && abs((float) ($result['amount'] ?? 0) - (float) $payment->amount) < 0.01
            && ($result['currency_type'] ?? config('sslcommerz.currency')) === config('sslcommerz.currency');

        if (! $valid) {
            return false;
        }

        DB::transaction(function () use ($payment, $valId) {
            $locked = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($locked->status === 'Success') {
                return;
            }

            $invoice = Invoice::lockForUpdate()->findOrFail($locked->invoice_id);

            $locked->update([
                'status' => 'Success',
                'gateway_val_id' => $valId,
                'payment_date' => now()->toDateString(),
            ]);

            $invoice->syncPayments();
        });

        return true;
    }
}