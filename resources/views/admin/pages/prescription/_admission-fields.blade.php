{{--
    Admission section for prescription create / edit.
    The doctor only SUGGESTS admission. The receptionist arranges ward & bed later.
    Submitted field: admission_advised (0|1)
--}}
@php
    $existingAdmission = isset($prescription) ? $prescription->admission : null;
    $advisedChoice = (string) old('admission_advised', isset($prescription) && $prescription->admission_advised ? '1' : '0');
    $statusColors = ['Admitted' => 'danger', 'Discharged' => 'success', 'Transferred' => 'info'];
@endphp

<div class="mb-4">
    <p class="fw-semibold mb-2">Admission</p>

    @if ($existingAdmission)
        {{-- Receptionist already admitted the patient: read-only summary --}}
        <div class="alert alert-info mb-0">
            <span class="badge bg-{{ $statusColors[$existingAdmission->status] ?? 'secondary' }} me-2">
                {{ $existingAdmission->status }}
            </span>
            Ward: <strong>{{ $existingAdmission->ward->name ?? '-' }}</strong>,
            Bed: <strong>{{ $existingAdmission->bed->bed_number ?? '-' }}</strong>
            &middot; {{ $existingAdmission->admission_date?->format('d M Y, h:i A') }}
            @if (in_array(auth()->user()->role_id, [1, 3]))
                <a href="{{ route('admissions.show', $existingAdmission->id) }}" class="ms-2">View admission</a>
            @endif
        </div>
    @else
        <div class="mb-1">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="admission_advised" id="advise-no" value="0"
                    @checked($advisedChoice !== '1')>
                <label class="form-check-label" for="advise-no">No admission needed (tests / treatment, then go home)</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="admission_advised" id="advise-yes" value="1"
                    @checked($advisedChoice === '1')>
                <label class="form-check-label" for="advise-yes">Suggest admission</label>
            </div>
            <x-admin.error-msg name="admission_advised" />
        </div>
        <div class="text-muted fs-sm">
            If suggested, the receptionist will choose the ward &amp; bed and admit the patient.
        </div>
    @endif
</div>
