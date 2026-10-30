<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Ward;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admissions = Admission::with(['patient', 'doctor.user', 'ward', 'bed'])
            ->orderBy('id', 'desc')
            ->get();

        $totalAdmissions   = Admission::count();
        $currentlyAdmitted = Admission::where('status', 'Admitted')->count();
        $dischargedCount   = Admission::where('status', 'Discharged')->count();

        return response()->json([
            'success'           => true,
            'admissions'        => $admissions,
            'totalAdmissions'   => $totalAdmissions,
            'currentlyAdmitted' => $currentlyAdmitted,
            'dischargedCount'   => $dischargedCount,
        ]);
    }

    // create() ar edit() lagbe na, karon form React-e.
    // Dropdown-er data options() theke ashbe: api.get("admissions/options")

    /**
     * Form-er dropdown-er data (patients, doctors, wards, beds).
     *
     * ?prescription_id=  dile oi prescription-er patient/doctor pathay (form pre-fill-er jonno)
     * ?admission_id=     dile (edit-e) oi admission-er bed-o beds-er moddhe thake
     *
     * Route-e apiResource-er ager e boshate hobe, noile "options" ke {id} bhabe.
     */
    public function options(Request $request)
    {
        $prescription = null;
        if ($request->filled('prescription_id')) {
            $prescription = Prescription::with(['patient', 'doctor.user'])
                ->find($request->query('prescription_id'));

            if (!$prescription) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Prescription not found',
                ], 404);
            }

            if ($prescription->admission_id) {
                return response()->json([
                    'error'        => true,
                    'message'      => 'This prescription already has an admission.',
                    'admission_id' => $prescription->admission_id,
                ], 409);
            }
        }

        // Available bed; edit-e ager bed-o thakbe
        $currentBedId = null;
        if ($request->filled('admission_id')) {
            $currentBedId = Admission::where('id', $request->query('admission_id'))->value('bed_id');
        }

        $beds = Bed::with('ward')
            ->where('status', 'Available')
            ->when($currentBedId, fn($q) => $q->orWhere('id', $currentBedId))
            ->get();

        return response()->json([
            'success'      => true,
            'patients'     => Patient::orderBy('name')->get(),
            'doctors'      => Doctor::with(['user', 'department'])->get(),
            'wards'        => Ward::orderBy('name')->get(),
            'beds'         => $beds,
            'prescription' => $prescription,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'patient_id'      => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:doctors,id',
            'ward_id'         => 'required|exists:wards,id',
            'bed_id'          => 'required|exists:beds,id',
            'admission_date'  => 'required|date',
            'discharge_date'  => 'nullable|date|after_or_equal:admission_date',
            'status'          => 'required|in:Admitted,Discharged,Transferred',
        ]);

        // Ekta patient-er ekshathe shudhu ekta active admission thakte pare
        if ($validated['status'] === 'Admitted' && $this->hasActiveAdmission($validated['patient_id'])) {
            return $this->validationError('patient_id', 'This patient is already admitted. Discharge the current admission first.');
        }

        // Race condition thekhe bachte sob check transaction + lock-er bhitore
        $result = DB::transaction(function () use ($validated) {
            // 1) Prescription valid kina
            $prescription = null;
            if (!empty($validated['prescription_id'])) {
                $prescription = Prescription::lockForUpdate()->find($validated['prescription_id']);

                if ($prescription->admission_id) {
                    return ['error' => ['patient_id', 'This prescription is already linked to an admission.']];
                }
                if (!$prescription->admission_advised) {
                    return ['error' => ['patient_id', 'Admission was not advised in this prescription.']];
                }
                if ((int) $prescription->patient_id !== (int) $validated['patient_id']) {
                    return ['error' => ['patient_id', 'The patient does not match the prescription.']];
                }
            }

            // 2) Bed oi ward-er kina, ar available kina
            $bed = Bed::lockForUpdate()->find($validated['bed_id']);

            if ((int) $bed->ward_id !== (int) $validated['ward_id']) {
                return ['error' => ['bed_id', 'The selected bed does not belong to this ward.']];
            }
            if ($validated['status'] === 'Admitted' && $bed->status !== 'Available') {
                return ['error' => ['bed_id', 'The selected bed is not available.']];
            }

            // 3) Admission toiri
            $admission = Admission::create(
                collect($validated)->except('prescription_id')->toArray()
            );

            // Patient admit hole bed Occupied hoy
            if ($admission->status === 'Admitted') {
                $bed->update(['status' => 'Occupied']);
            }

            // Prescription-er shathe link
            if ($prescription) {
                $prescription->update(['admission_id' => $admission->id]);
            }

            return ['admission' => $admission];
        });

        if (isset($result['error'])) {
            return $this->validationError(...$result['error']);
        }

        $admission = $result['admission']->load(['patient', 'doctor.user', 'ward', 'bed']);

        return response()->json([
            'success'   => 'Admission recorded successfully',
            'admission' => $admission,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admission = Admission::with(['patient', 'doctor.user', 'doctor.department', 'ward', 'bed'])->find($id);

        if ($admission) {
            return response()->json([
                'success'   => true,
                'admission' => $admission,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Admission not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'doctor_id'      => 'required|exists:doctors,id',
            'ward_id'        => 'required|exists:wards,id',
            'bed_id'         => 'required|exists:beds,id',
            'admission_date' => 'required|date',
            'discharge_date' => 'nullable|date|after_or_equal:admission_date',
            'status'         => 'required|in:Admitted,Discharged,Transferred',
        ]);

        $admission = Admission::find($id);

        if (!$admission) {
            return response()->json([
                'error'   => true,
                'message' => 'Admission not found',
            ], 404);
        }

        if (
            $validated['status'] === 'Admitted'
            && $this->hasActiveAdmission($validated['patient_id'], $admission->id)
        ) {
            return $this->validationError('patient_id', 'This patient already has another active admission.');
        }

        $previousBedId = $admission->bed_id;

        $admission->update($validated);

        // Bed-er status admission-er status-er shathe mil rakhi
        if ($admission->status === 'Discharged') {
            $admission->bed->update(['status' => 'Available']);
        } elseif ($admission->status === 'Admitted') {
            $admission->bed->update(['status' => 'Occupied']);

            // Patient onno bed-e gele ager bed free kori
            if ($previousBedId != $admission->bed_id) {
                Bed::where('id', $previousBedId)->update(['status' => 'Available']);
            }
        }

        $admission->load(['patient', 'doctor.user', 'ward', 'bed']);

        return response()->json([
            'success'   => true,
            'message'   => 'Admission updated successfully',
            'admission' => $admission,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admission = Admission::find($id);

        if (!$admission) {
            return response()->json([
                'error'   => true,
                'message' => 'Admission not found',
            ], 404);
        }

        try {
            DB::transaction(function () use ($admission) {
                if ($admission->status === 'Admitted') {
                    Bed::where('id', $admission->bed_id)->update(['status' => 'Available']);
                }

                $admission->delete();
            });
        } catch (QueryException $e) {
            // Prescription/invoice-er shathe linked thakle foreign key delete hote dey na
            return response()->json([
                'error'   => true,
                'message' => 'This admission is linked to other records, so it cannot be deleted',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Admission deleted successfully',
        ]);
    }

    /**
     * Ei patient-er ekhon kono Admitted (active) admission ache kina?
     */
    private function hasActiveAdmission(int|string $patientId, ?int $exceptId = null): bool
    {
        return Admission::where('patient_id', $patientId)
            ->where('status', 'Admitted')
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Laravel-er nijer 422 format-e custom validation error (React err.response.data.errors pay).
     */
    private function validationError(string $field, string $message)
    {
        return response()->json([
            'message' => $message,
            'errors'  => [$field => [$message]],
        ], 422);
    }
}
