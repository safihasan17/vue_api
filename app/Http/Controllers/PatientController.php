<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalPatients = Patient::count();

        $newThisMonth = Patient::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalGuardians = Patient::whereNotNull('guardian_name')->count();

        $patients = Patient::orderby('id', 'desc')->get();

        // return view('admin.pages.patient.index', compact('patients', 'totalPatients', 'newThisMonth', 'totalGuardians'));
        return response()->json([
            'success'        => true,
            'patients'       => $patients,
            'totalPatients'  => $totalPatients,
            'newThisMonth'   => $newThisMonth,
            'totalGuardians' => $totalGuardians,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.patient.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);

        if ($patient) {
            // return redirect()
            //     ->route('patients.index')
            //     ->with('success', 'Patient added successfully.');
            return response()->json([
                'success' => 'Patient created successfully',
                'patient' => $patient
            ]);
        } else {
            return response()->json([
                'error' => 'Patient not created. Try again later'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::find($id);

        // return view('admin.pages.patient.show', compact('patient'));
        if ($patient) {
            return response()->json([
                'success' => true,
                'patient' => $patient
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Patient not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('admin.pages.patient.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'error' => true,
                'message' => 'Patient not found'
            ], 404);
        }

        if ($patient->update($validated)) {
            // return redirect()
            //     ->route('patients.index')
            //     ->with('success', 'Patient updated successfully.');
            return response()->json([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $patient
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Patient is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::find($id);

        if ($patient) {
            $patient->delete();
            return response()->json([
                'success' => true,
                'message' => 'Patient deleted successfully'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Patient not found'
            ], 404);
        }
    }

    public function search(Request $request)
    {
        $search = $request->query('q');

        $patients = Patient::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($patients);
    }
}