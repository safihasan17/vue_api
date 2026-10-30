<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Services\UploadImgService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalDoctors = Doctor::count();

        $totalDepartments = Department::count();

        $newThisMonth = Doctor::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $doctors = Doctor::with(['user:id,name', 'department:id,name'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success'          => true,
            'doctors'          => $doctors,
            'totalDoctors'     => $totalDoctors,
            'totalDepartments' => $totalDepartments,
            'newThisMonth'     => $newThisMonth,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // User/Department-er dropdown list React theke
    // api.get("users") ar api.get("departments") diye ashbe.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'department_id'    => 'nullable|exists:departments,id',
            'specialization'   => 'nullable|string|max:100',
            'qualification'    => 'nullable|string|max:150',
            'consultation_fee' => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ], [
            'image.max' => 'Image size is too large. Maximum size is 500KB',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = UploadImgService::upload(
                $request->image,
                'uploads/doctors'
            );
        }

        $doctor = Doctor::create($validated);
        $doctor->load(['user:id,name,email,phone', 'department:id,name']);

        return response()->json([
            'success' => 'Doctor created successfully',
            'doctor'  => $doctor,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::with(['user:id,name,email,phone', 'department:id,name'])->find($id);

        if ($doctor) {
            return response()->json([
                'success' => true,
                'doctor'  => $doctor,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Doctor not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * Note: image upload-er shomoy React theke PUT na pathiye POST pathabe
     * ar FormData-te `_method=PUT` dibe, noile Laravel file pabe na.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'department_id'    => 'nullable|exists:departments,id',
            'specialization'   => 'nullable|string|max:100',
            'qualification'    => 'nullable|string|max:150',
            'consultation_fee' => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ], [
            'image.max' => 'Image size is too large. Maximum size is 500KB',
        ]);

        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'error'   => true,
                'message' => 'Doctor not found',
            ], 404);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = UploadImgService::upload(
                $request->image,
                'uploads/doctors'
            );
        }

        if ($doctor->update($validated)) {
            $doctor->load(['user:id,name,email,phone', 'department:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully',
                'doctor'  => $doctor,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Doctor is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = Doctor::find($id);

        if ($doctor) {
            $doctor->delete();
            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Doctor not found',
            ], 404);
        }
    }

    public function search(Request $request)
    {
        $search = $request->query('q');

        $doctors = Doctor::with(['user', 'department'])
            ->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($doctor) {
                return [
                    'id'   => $doctor->id,
                    'name' => ($doctor->user->name ?? 'Doctor #' . $doctor->id)
                        . ($doctor->department ? ' — ' . $doctor->department->name : ''),
                ];
            });

        return response()->json($doctors);
    }
}