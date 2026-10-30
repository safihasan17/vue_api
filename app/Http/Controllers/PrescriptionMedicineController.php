<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionMedicine;
use Illuminate\Http\Request;

class PrescriptionMedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Filter: ?prescription_id=  (ek prescription-er shob medicine)
     */
    public function index(Request $request)
    {
        $query = PrescriptionMedicine::with('medicine:id,name');

        if ($request->filled('prescription_id')) {
            $query->where('prescription_id', $request->prescription_id);
        }

        $prescriptionMedicines = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success'               => true,
            'prescriptionMedicines' => $prescriptionMedicines,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Prescription form-e medicine row gulo shadharonoto PrescriptionController diye save hoy,
    // eta shudhu ekta row alada kore add/edit/delete korar jonno.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePrescriptionMedicine($request);

        $prescriptionMedicine = PrescriptionMedicine::create($validated);
        $prescriptionMedicine->load('medicine:id,name');

        return response()->json([
            'success'              => 'Prescription medicine added successfully',
            'prescriptionMedicine' => $prescriptionMedicine,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prescriptionMedicine = PrescriptionMedicine::with('medicine')->find($id);

        if ($prescriptionMedicine) {
            return response()->json([
                'success'              => true,
                'prescriptionMedicine' => $prescriptionMedicine,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription medicine not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePrescriptionMedicine($request);

        $prescriptionMedicine = PrescriptionMedicine::find($id);

        if (!$prescriptionMedicine) {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription medicine not found',
            ], 404);
        }

        if ($prescriptionMedicine->update($validated)) {
            $prescriptionMedicine->load('medicine:id,name');

            return response()->json([
                'success'              => true,
                'message'              => 'Prescription medicine updated successfully',
                'prescriptionMedicine' => $prescriptionMedicine,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription medicine is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prescriptionMedicine = PrescriptionMedicine::find($id);

        if ($prescriptionMedicine) {
            $prescriptionMedicine->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prescription medicine deleted successfully',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Prescription medicine not found',
            ], 404);
        }
    }

    // Validation PrescriptionController-er medicines.* rule-er moto
    private function validatePrescriptionMedicine(Request $request): array
    {
        return $request->validate([
            'prescription_id' => ['required', 'exists:prescriptions,id'],
            'medicine_id'     => ['required', 'exists:medicines,id'],
            'dosage'          => ['nullable', 'string', 'max:100'],
            'duration'        => ['nullable', 'string', 'max:50'],
            'instructions'    => ['nullable', 'string'],
        ]);
    }
}
