<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicinePurchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedicinePurchaseController extends Controller
{
    public function index()
    {
        $purchases = MedicinePurchase::with(['supplier:id,name', 'medicine:id,name'])
            ->latest('purchase_date')->latest('id')->get();

        return response()->json([
            'success'        => true,
            'purchases'      => $purchases,
            'totalPurchases' => $purchases->count(),
            'totalUnits'     => (int) $purchases->sum('quantity'),
            'totalSpent'     => (float) MedicinePurchase::sum(DB::raw('quantity * purchase_price')),
            'monthSpent'     => (float) MedicinePurchase::whereYear('purchase_date', now()->year)
                ->whereMonth('purchase_date', now()->month)
                ->sum(DB::raw('quantity * purchase_price')),
        ]);
    }

    public function options()
    {
        return response()->json([
            'success'   => true,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'medicines' => Medicine::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** Save the purchase AND add the quantity to the medicine's stock. */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $purchase = DB::transaction(function () use ($data) {
            $purchase = MedicinePurchase::create($data);
            Medicine::where('id', $data['medicine_id'])->increment('stock_quantity', $data['quantity']);

            return $purchase;
        });

        return response()->json([
            'success'  => 'Purchase recorded and stock updated',
            'purchase' => $purchase,
        ]);
    }

    public function show(string $id)
    {
        $purchase = MedicinePurchase::with(['supplier', 'medicine'])->find($id);

        if (!$purchase) {
            return response()->json(['error' => true, 'message' => 'Purchase not found'], 404);
        }

        return response()->json(['success' => true, 'purchase' => $purchase]);
    }

    /** Keep stock in sync when quantity / medicine changes. */
    public function update(Request $request, string $id)
    {
        $data = $this->validated($request);

        if (!MedicinePurchase::find($id)) {
            return response()->json(['error' => true, 'message' => 'Purchase not found'], 404);
        }

        DB::transaction(function () use ($data, $id) {
            $purchase = MedicinePurchase::lockForUpdate()->findOrFail($id);
            $oldMedicine = Medicine::lockForUpdate()->findOrFail($purchase->medicine_id);

            if ((int) $data['medicine_id'] === (int) $purchase->medicine_id) {
                $delta = $data['quantity'] - $purchase->quantity;

                if ($delta < 0 && $oldMedicine->stock_quantity < abs($delta)) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Cannot reduce quantity: only ' . $oldMedicine->stock_quantity . ' in stock (some of it was already used).',
                    ]);
                }

                $oldMedicine->increment('stock_quantity', $delta);
            } else {
                if ($oldMedicine->stock_quantity < $purchase->quantity) {
                    throw ValidationException::withMessages([
                        'medicine_id' => 'Cannot change medicine: the old medicine no longer has enough stock to reverse this purchase.',
                    ]);
                }

                $oldMedicine->decrement('stock_quantity', $purchase->quantity);
                Medicine::where('id', $data['medicine_id'])->increment('stock_quantity', $data['quantity']);
            }

            $purchase->update($data);
        });

        return response()->json(['success' => true, 'message' => 'Purchase updated and stock adjusted']);
    }

    /** Deleting a purchase removes its quantity from stock. */
    public function destroy(string $id)
    {
        if (!MedicinePurchase::find($id)) {
            return response()->json(['error' => true, 'message' => 'Purchase not found'], 404);
        }

        try {
            DB::transaction(function () use ($id) {
                $purchase = MedicinePurchase::lockForUpdate()->findOrFail($id);
                $medicine = Medicine::lockForUpdate()->findOrFail($purchase->medicine_id);

                if ($medicine->stock_quantity < $purchase->quantity) {
                    throw new \RuntimeException('Cannot delete: current stock is lower than this purchase quantity (some was already used).');
                }

                $medicine->decrement('stock_quantity', $purchase->quantity);
                $purchase->delete();
            });
        } catch (\RuntimeException $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 409);
        }

        return response()->json(['success' => true, 'message' => 'Purchase deleted and stock adjusted']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'medicine_id'    => 'required|exists:medicines,id',
            'quantity'       => 'required|integer|min:1|max:1000000',
            'purchase_price' => 'required|numeric|min:0|max:99999999.99',
            'purchase_date'  => 'required|date',
        ]);
    }
}
