<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::orderBy('name', 'asc')->get();

        return response()->json([
            'success'          => true,
            'departments'      => $departments,
            'totalDepartments' => $departments->count(),
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department              = new Department();
        $department->name        = $request->name;
        $department->description = $request->description;

        if ($department->save()) {
            return response()->json([
                'success'    => 'Department created successfully',
                'department' => $department,
            ]);
        } else {
            return response()->json([
                'error' => 'Department not created. Try again later',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::find($id);

        if ($department) {
            return response()->json([
                'success'    => true,
                'department' => $department,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Department not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => "required|string|max:100|unique:departments,name,$id",
            'description' => 'nullable|string',
        ]);

        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'error'   => true,
                'message' => 'Department not found',
            ], 404);
        }

        $department->name        = $request->name;
        $department->description = $request->description;

        if ($department->save()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Department updated successfully',
                'department' => $department,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Department is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'error'   => true,
                'message' => 'Department not found',
            ], 404);
        }

        try {
            $department->delete();
        } catch (QueryException $e) {
            // kono doctor ei department use korle foreign key delete atkabe
            return response()->json([
                'error'   => true,
                'message' => 'Department is in use by doctors and cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ]);
    }
}