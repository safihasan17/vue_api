<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Filter: ?ward_id=  , ?status=
     */
    public function index(Request $request)
    {
        $beds = Bed::with('ward')
            ->when($request->filled('ward_id'), fn ($q) => $q->where('ward_id', $request->ward_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('ward_id')
            ->orderBy('bed_number')
            ->get();

        $totalBeds        = Bed::count();
        $availableCount   = Bed::where('status', 'Available')->count();
        $occupiedCount    = Bed::where('status', 'Occupied')->count();
        $maintenanceCount = Bed::where('status', 'Maintenance')->count();

        return response()->json([
            'success'          => true,
            'beds'             => $beds,
            'totalBeds'        => $totalBeds,
            'availableCount'   => $availableCount,
            'occupiedCount'    => $occupiedCount,
            'maintenanceCount' => $maintenanceCount,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Ward dropdown-er jonno GET wards use hobe (WardController@index-er `wards`).

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateBed($request);

        $bed = Bed::create($validated);
        $bed->load('ward');

        return response()->json([
            'success' => 'Bed added successfully',
            'bed'     => $bed,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bed = Bed::with('ward')->find($id);

        if ($bed) {
            return response()->json([
                'success' => true,
                'bed'     => $bed,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Bed not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validateBed($request);

        $bed = Bed::find($id);

        if (!$bed) {
            return response()->json([
                'error'   => true,
                'message' => 'Bed not found',
            ], 404);
        }

        if ($bed->update($validated)) {
            $bed->load('ward');

            return response()->json([
                'success' => true,
                'message' => 'Bed updated successfully',
                'bed'     => $bed,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Bed is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bed = Bed::find($id);

        if (!$bed) {
            return response()->json([
                'error'   => true,
                'message' => 'Bed not found',
            ], 404);
        }

        try {
            $bed->delete();
        } catch (QueryException $e) {
            // Kono admission-e use hole foreign key delete hote dey na
            return response()->json([
                'error'   => true,
                'message' => 'This bed is already used in admissions, so it cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bed deleted successfully',
        ]);
    }

    private function validateBed(Request $request): array
    {
        return $request->validate([
            'ward_id'    => 'required|exists:wards,id',
            'bed_number' => 'required|string|max:20',
            'status'     => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);
    }
}
