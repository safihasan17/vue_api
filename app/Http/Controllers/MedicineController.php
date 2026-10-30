<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Filter: ?q=  (name / generic_name), ?category=  , ?stock=low|out|in
     */
    public function index(Request $request)
    {
        $low = Medicine::LOW_STOCK_THRESHOLD;

        $query = Medicine::query();

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('generic_name', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->stock === 'low') {
            $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<', $low);
        } elseif ($request->stock === 'out') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($request->stock === 'in') {
            $query->where('stock_quantity', '>=', $low);
        }

        $medicines = $query->orderBy('name')->get();

        $categories = Medicine::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $totalMedicines  = Medicine::count();
        $lowStockCount   = Medicine::where('stock_quantity', '>', 0)->where('stock_quantity', '<', $low)->count();
        $outOfStockCount = Medicine::where('stock_quantity', '<=', 0)->count();

        return response()->json([
            'success'         => true,
            'medicines'       => $medicines,
            'categories'      => $categories,
            'lowStockLimit'   => $low,
            'totalMedicines'  => $totalMedicines,
            'lowStockCount'   => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateMedicine($request);

        $medicine = Medicine::create($validated);

        return response()->json([
            'success'  => 'Medicine created successfully',
            'medicine' => $medicine,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $medicine = Medicine::find($id);

        if ($medicine) {
            return response()->json([
                'success'  => true,
                'medicine' => $medicine,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Medicine not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validateMedicine($request);

        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json([
                'error'   => true,
                'message' => 'Medicine not found',
            ], 404);
        }

        if ($medicine->update($validated)) {
            return response()->json([
                'success'  => true,
                'message'  => 'Medicine updated successfully',
                'medicine' => $medicine,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Medicine is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json([
                'error'   => true,
                'message' => 'Medicine not found',
            ], 404);
        }

        try {
            $medicine->delete();
        } catch (QueryException $e) {
            // Kono prescription/purchase-e use hole foreign key delete hote dey na
            return response()->json([
                'error'   => true,
                'message' => 'This medicine is already used in other records, so it cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Medicine deleted successfully',
        ]);
    }

    // Tomar Medicine table-e aro column thakle (manufacturer, expiry_date ...) shudhu eikhane add korlei hobe.
    private function validateMedicine(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'generic_name'   => ['nullable', 'string', 'max:150'],
            'category'       => ['nullable', 'string', 'max:100'],
            'unit_price'     => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);
    }
}
