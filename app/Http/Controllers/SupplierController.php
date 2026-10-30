<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->orderBy('name')->get();

        return response()->json([
            'success'        => true,
            'suppliers'      => $suppliers,
            'totalSuppliers' => $suppliers->count(),
        ]);
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($this->validated($request));

        return response()->json([
            'success'  => 'Supplier added successfully',
            'supplier' => $supplier,
        ]);
    }

    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['error' => true, 'message' => 'Supplier not found'], 404);
        }

        $purchases = $supplier->purchases()->with('medicine:id,name')
            ->latest('purchase_date')->latest('id')->take(10)->get();

        return response()->json([
            'success'   => true,
            'supplier'  => $supplier,
            'purchases' => $purchases,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['error' => true, 'message' => 'Supplier not found'], 404);
        }

        $supplier->update($this->validated($request));

        return response()->json(['success' => true, 'message' => 'Supplier updated successfully']);
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['error' => true, 'message' => 'Supplier not found'], 404);
        }

        if ($supplier->purchases()->exists()) {
            return response()->json([
                'error'   => true,
                'message' => 'This supplier has purchase records and cannot be deleted.',
            ], 409);
        }

        $supplier->delete();

        return response()->json(['success' => true, 'message' => 'Supplier deleted successfully']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => 'required|string|max:100',
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
        ]);
    }
}
