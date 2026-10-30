<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalWards = Ward::count();

        $totalFloors = Ward::whereNotNull('floor')->distinct()->count('floor');

        $totalTypes = Ward::whereNotNull('type')->distinct()->count('type');

        $wards = Ward::orderBy('id', 'desc')->get();

        return response()->json([
            'success'     => true,
            'wards'       => $wards,
            'totalWards'  => $totalWards,
            'totalFloors' => $totalFloors,
            'totalTypes'  => $totalTypes,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Dropdown-er data options() theke ashbe: api.get("wards/options")

    /**
     * Floor ar type-er dropdown option.
     * Route-e apiResource-er ager e boshate hobe, noile "options" ke {id} bhabe.
     */
    public function options()
    {
        return response()->json([
            'success' => true,
            'floors'  => $this->floorOptions(),
            'types'   => $this->typeOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|min:3|max:100|unique:wards,name',
            'floor' => 'required|max:20',
            'type'  => 'required|max:50',
        ]);

        $ward        = new Ward();
        $ward->name  = $request->name;
        $ward->floor = $request->floor;
        $ward->type  = $request->type;

        if ($ward->save()) {
            return response()->json([
                'success' => 'Ward created successfully',
                'ward'    => $ward,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Ward is not created',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ward = Ward::find($id);

        if ($ward) {
            return response()->json([
                'success' => true,
                'ward'    => $ward,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Ward not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'  => ['required', 'min:3', 'max:100', Rule::unique('wards', 'name')->ignore($id)],
            'floor' => 'required|max:20',
            'type'  => 'required|max:50',
        ]);

        $ward = Ward::find($id);

        if (!$ward) {
            return response()->json([
                'error'   => true,
                'message' => 'Ward not found',
            ], 404);
        }

        $ward->name  = $request->name;
        $ward->floor = $request->floor;
        $ward->type  = $request->type;

        if ($ward->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Ward updated successfully',
                'ward'    => $ward,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Ward is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ward = Ward::find($id);

        if (!$ward) {
            return response()->json([
                'error'   => true,
                'message' => 'Ward not found',
            ], 404);
        }

        try {
            $ward->delete();
        } catch (QueryException $e) {
            // Ward-e bed ba admission thakle foreign key delete hote dey na
            return response()->json([
                'error'   => true,
                'message' => 'This ward is already used by beds or admissions, so it cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ward deleted successfully',
        ]);
    }

    /**
     * Floor options: values already in the wards table + default floors.
     */
    private function floorOptions()
    {
        return Ward::whereNotNull('floor')->distinct()->pluck('floor')
            ->merge(['1st Floor', '2nd Floor', '3rd Floor', '4th Floor'])
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();
    }

    /**
     * Type options: values already in the wards table + default types.
     */
    private function typeOptions()
    {
        return Ward::whereNotNull('type')->distinct()->pluck('type')
            ->merge(['General', 'Cabin', 'Critical Care', 'Pediatric'])
            ->unique()
            ->sort()
            ->values();
    }
}
