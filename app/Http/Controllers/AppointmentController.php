<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient:id,name', 'doctor.user:id,name', 'doctor.department:id,name']);

        // if (Auth::user()->role_id == 2) {
        //     $query->whereHas('doctor', function ($q) {
        //         $q->where('user_id', Auth::user()->id);
        //     });
        // }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%");
                })->orWhereHas('doctor.user', function ($d) use ($search) {
                    $d->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('id', 'desc')->get();

        $statsQuery = Appointment::query();

        // if (Auth::user()->role_id == 2) {
        //     $statsQuery->whereHas('doctor', function ($q) {
        //         $q->where('user_id', Auth::user()->id);
        //     });
        // }

        $todayCount     = (clone $statsQuery)->whereDate('appointment_date', today())->count();
        $pendingCount   = (clone $statsQuery)->whereIn('status', ['Scheduled', 'Checked-in', 'Waiting'])->count();
        $completedCount = (clone $statsQuery)->where('status', 'Completed')->count();
        $cancelledCount = (clone $statsQuery)->where('status', 'Cancelled')->count();

        return response()->json([
            'success'        => true,
            'appointments'   => $appointments,
            'todayCount'     => $todayCount,
            'pendingCount'   => $pendingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Patient/Doctor-er dropdown list React theke
    // api.get("patients") ar api.get("doctors") diye ashbe.

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateAppointment($request);

        $appointment = Appointment::create($validated);
        $appointment->load(['patient:id,name', 'doctor.user:id,name', 'doctor.department:id,name']);

        return response()->json([
            'success'     => true,
            'message'     => 'Appointment booked successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with(['patient', 'doctor.user', 'doctor.department'])->find($id);

        if ($appointment) {
            return response()->json([
                'success'     => true,
                'appointment' => $appointment,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Appointment not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validateAppointment($request);

        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'error'   => true,
                'message' => 'Appointment not found',
            ], 404);
        }

        if ($appointment->update($validated)) {
            $appointment->load(['patient:id,name', 'doctor.user:id,name', 'doctor.department:id,name']);

            return response()->json([
                'success'     => true,
                'message'     => 'Appointment updated successfully',
                'appointment' => $appointment,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Appointment is not updated',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::find($id);

        if ($appointment) {
            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Appointment not found',
            ], 404);
        }
    }

    private function validateAppointment(Request $request): array
    {
        return $request->validate([
            'patient_id'       => ['required', 'exists:patients,id'],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
            'status'           => ['required', Rule::in(['Scheduled', 'Checked-in', 'Waiting', 'Completed', 'Cancelled'])],
            'reason'           => ['nullable', 'string', 'max:1000'],
        ]);
    }
}