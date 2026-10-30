<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalLabTests = LabTest::count();

        $labTests = LabTest::orderBy('test_name')->get();

        return response()->json([
            'success'       => true,
            'labTests'      => $labTests,
            'totalLabTests' => $totalLabTests,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateLabTest($request);

        $labTest = LabTest::create($validated);

        return response()->json([
            'success' => 'Lab test created successfully',
            'labTest' => $labTest,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $labTest = LabTest::find($id);

        if ($labTest) {
            return response()->json([
                'success' => true,
                'labTest' => $labTest,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validateLabTest($request);

        $labTest = LabTest::find($id);

        if (!$labTest) {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test not found',
            ], 404);
        }

        if ($labTest->update($validated)) {
            return response()->json([
                'success' => true,
                'message' => 'Lab test updated successfully',
                'labTest' => $labTest,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $labTest = LabTest::find($id);

        if (!$labTest) {
            return response()->json([
                'error'   => true,
                'message' => 'Lab test not found',
            ], 404);
        }

        try {
            $labTest->delete();
        } catch (QueryException $e) {
            // Kono lab order-e use hole foreign key delete hote dey na
            return response()->json([
                'error'   => true,
                'message' => 'This lab test is already used in lab orders, so it cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lab test deleted successfully',
        ]);
    }

    // Tomar LabTest table-e aro column thakle (description, sample_type ...) shudhu eikhane add korlei hobe.
    private function validateLabTest(Request $request): array
    {
        return $request->validate([
            'test_name' => ['required', 'string', 'max:150'],
            'price'     => ['required', 'numeric', 'min:0'],
        ]);
    }
}
