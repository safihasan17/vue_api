<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\LabTest;
use App\Models\LabTestOrder;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LabTestOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labTestOrders = LabTestOrder::with(['patient:id,name', 'doctor.user:id,name', 'test:id,test_name,price'])
            ->latest('order_date')
            ->latest('id')
            ->get();

        $pendingCount    = LabTestOrder::where('status', 'Pending')->count();
        $inProgressCount = LabTestOrder::where('status', 'In Progress')->count();
        $completedCount  = LabTestOrder::where('status', 'Completed')->count();
        $cancelledCount  = LabTestOrder::where('status', 'Cancelled')->count();

        return response()->json([
            'success'         => true,
            'labTestOrders'   => $labTestOrders,
            'pendingCount'    => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'completedCount'  => $completedCount,
            'cancelledCount'  => $cancelledCount,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Dropdown-er data options() theke ashbe: api.get("lab-test-orders/options")

    /**
     * Form-er dropdown-er data (patients, doctors, lab tests).
     * Route-e apiResource-er ager e boshate hobe, noile "options" ke {id} bhabe.
     */
    public function options()
    {
        return response()->json([
            'success'  => true,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'phone']),
            'doctors'  => Doctor::with(['user:id,name', 'department:id,name'])->get(),
            'labTests' => LabTest::orderBy('test_name')->get(['id', 'test_name', 'price']),
        ]);
    }

    /**
     * Store newly created resources in storage (one order per selected test).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'          => 'required|exists:patients,id',
            'doctor_id'           => 'required|exists:doctors,id',
            'status'              => 'required|in:Pending,In Progress,Completed,Cancelled',
            'result'              => 'nullable|string',
            'order_date'          => 'required|date',
            'lab_tests'           => 'required|array|min:1',
            'lab_tests.*.test_id' => 'required|distinct|exists:lab_tests,id',
        ]);

        $orders = DB::transaction(function () use ($validated) {
            $created = [];

            foreach ($validated['lab_tests'] as $row) {
                $created[] = LabTestOrder::create([
                    'patient_id' => $validated['patient_id'],
                    'doctor_id'  => $validated['doctor_id'],
                    'test_id'    => $row['test_id'],
                    'status'     => $validated['status'],
                    'result'     => $validated['result'] ?? null,
                    'order_date' => $validated['order_date'],
                ]);
            }

            return collect($created);
        });

        $orders->each->load(['patient:id,name', 'doctor.user:id,name', 'test:id,test_name,price']);

        return response()->json([
            'success' => 'Lab test order created successfully',
            'orders'  => $orders,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $labTestOrder = LabTestOrder::with([
            'patient',
            'doctor.user:id,name,email,phone',
            'doctor.department:id,name',
            'test',
        ])->find($id);

        if ($labTestOrder) {
            return response()->json([
                'success'       => true,
                'labTestOrder'  => $labTestOrder,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test order not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id'  => 'required|exists:doctors,id',
            'test_id'    => 'required|exists:lab_tests,id',
            'status'     => 'required|in:Pending,In Progress,Completed,Cancelled',
            'result'     => 'nullable|string',
            'order_date' => 'required|date',
        ]);

        $labTestOrder = LabTestOrder::find($id);

        if (!$labTestOrder) {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test order not found',
            ], 404);
        }

        if ($labTestOrder->update($validated)) {
            $labTestOrder->load(['patient:id,name', 'doctor.user:id,name', 'test:id,test_name,price']);

            return response()->json([
                'success'      => true,
                'message'      => 'Lab test order updated successfully',
                'labTestOrder' => $labTestOrder,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test order is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $labTestOrder = LabTestOrder::find($id);

        if ($labTestOrder) {
            $labTestOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lab test order deleted successfully',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test order not found',
            ], 404);
        }
    }

    /**
     * Lab order theke invoice generate kore.
     * POST /lab-test-orders/{id}/generate-invoice
     */
    public function generateInvoice(string $id)
    {
        $labTestOrder = LabTestOrder::find($id);

        if (!$labTestOrder) {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test order not found',
            ], 404);
        }

        if ($labTestOrder->prescription_id) {
            return response()->json([
                'error'   => true,
                'message' => 'This test belongs to a prescription. Generate the invoice from the prescription.',
            ], 422);
        }

        if ($labTestOrder->status === 'Cancelled') {
            return response()->json([
                'error'   => true,
                'message' => 'A cancelled test cannot be billed.',
            ], 422);
        }

        // order_date model-e date cast thakuk ba na thakuk, duto-tei kaj kore
        $orderDate = Carbon::parse($labTestOrder->order_date)->toDateString();

        // Ei patient-er ei tarikher shob test (cancelled/prescription-er bade) ekshathe
        $orders = LabTestOrder::with('test')
            ->where('patient_id', $labTestOrder->patient_id)
            ->whereDate('order_date', $orderDate)
            ->whereNull('prescription_id')
            ->where('status', '!=', 'Cancelled')
            ->get();

        $invoice = Invoice::where('patient_id', $labTestOrder->patient_id)
            ->whereNull('admission_id')
            ->whereNull('appointment_id')
            ->whereDate('invoice_date', today())
            ->where('status', 'Unpaid')
            ->first();

        if (!$invoice) {
            $invoice = Invoice::create([
                'patient_id'   => $labTestOrder->patient_id,
                'invoice_date' => today(),
                'status'       => 'Unpaid',
                'total_amount' => 0,
            ]);
        }

        foreach ($orders as $order) {
            $alreadyBilled = $invoice->items()
                ->where('item_type', 'Lab Test')
                ->where('item_reference_id', $order->test_id)
                ->exists();

            if (!$alreadyBilled) {
                $invoice->items()->create([
                    'item_type'         => 'Lab Test',
                    'item_reference_id' => $order->test_id,
                    'description'       => $order->test->test_name ?? 'Lab Test',
                    'amount'            => $order->test->price ?? 0,
                ]);
            }
        }

        $invoice->update(['total_amount' => $invoice->items()->sum('amount')]);

        return response()->json([
            'success'    => true,
            'message'    => 'Test invoice is ready to print',
            'invoice_id' => $invoice->id,
        ]);
    }
}
