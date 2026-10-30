@extends('admin.layouts.master')

@section('title', 'Appointments - Create')

@section('content')

    <div class="block block-rounded block-transparent mt-5">

        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Book Appointment" subtitle="Schedule a new appointment from this section">

                <a href="{{ route('appointments.index') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> back to appointments
                </a>

            </x-admin.phead>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('appointments.store') }}" method="POST">
                        @csrf
                        <div class="row">

                            {{-- Patient searchable input --}}
                            <div class="col-md-6 mb-4 position-relative">
                                <label class="form-label" for="ap-patient-search">Patient</label>
                                <input type="text" class="form-control" id="ap-patient-search"
                                    placeholder="Type patient name..." autocomplete="off"
                                    value="{{ old('patient_name') }}">
                                <input type="hidden" name="patient_id" id="ap-patient-id"
                                    value="{{ old('patient_id') }}">
                                <div id="ap-patient-results" class="list-group position-absolute w-100 shadow"
                                    style="z-index: 1000; display:none; max-height: 220px; overflow-y:auto;"></div>
                                <x-admin.error-msg name="patient_id" />
                            </div>

                            {{-- Doctor searchable input --}}
                            <div class="col-md-6 mb-4 position-relative">
                                <label class="form-label" for="ap-doctor-search">Doctor</label>

                                @if (auth()->user()->role_id == 2)
                                    @php
                                        $myDoctor = $doctors->firstWhere('user_id', auth()->id());
                                    @endphp
                                    <input type="text" class="form-control"
                                        value="{{ $myDoctor->user->name ?? '' }}{{ $myDoctor?->department ? ' — ' . $myDoctor->department->name : '' }}"
                                        disabled>
                                    <input type="hidden" name="doctor_id" value="{{ $myDoctor->id ?? '' }}">
                                @else
                                    <input type="text" class="form-control" id="ap-doctor-search"
                                        placeholder="Type doctor name..." autocomplete="off"
                                        value="{{ old('doctor_name') }}">
                                    <input type="hidden" name="doctor_id" id="ap-doctor-id"
                                        value="{{ old('doctor_id') }}">
                                    <div id="ap-doctor-results" class="list-group position-absolute w-100 shadow"
                                        style="z-index: 1000; display:none; max-height: 220px; overflow-y:auto;"></div>
                                @endif

                                <x-admin.error-msg name="doctor_id" />
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="ap-date">Date</label>
                                <input type="date" class="form-control" name="appointment_date"
                                    value="{{ old('appointment_date') }}">
                                <x-admin.error-msg name="appointment_date" />
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="ap-time">Time</label>
                                <input type="time" class="form-control" name="appointment_time"
                                    value="{{ old('appointment_time') }}">
                                <x-admin.error-msg name="appointment_time" />
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="ap-status">Status</label>
                                <select class="form-select" name="status" id="ap-status">
                                    <option value="Scheduled" @selected(old('status') == 'Scheduled')>Scheduled</option>
                                    <option value="Checked-in" @selected(old('status') == 'Checked-in')>Checked-in</option>
                                    <option value="Waiting" @selected(old('status') == 'Waiting')>Waiting</option>
                                    <option value="Completed" @selected(old('status') == 'Completed')>Completed</option>
                                    <option value="Cancelled" @selected(old('status') == 'Cancelled')>Cancelled</option>
                                </select>
                                <x-admin.error-msg name="status" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="ap-reason">Reason for Visit</label>
                                <textarea class="form-control" name="reason" id="ap-reason" rows="2">{{ old('reason') }}</textarea>
                                <x-admin.error-msg name="reason" />
                            </div>
                        </div>

                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary">Save
                                Appointment</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('script')
<script>
    function setupSearchField(inputId, hiddenId, resultsId, searchUrl) {
        const input   = document.getElementById(inputId);
        const hidden  = document.getElementById(hiddenId);
        const results = document.getElementById(resultsId);
        if (!input) return;

        let debounceTimer;

        input.addEventListener('input', function () {
            hidden.value = ''; 
            const q = this.value.trim();

            clearTimeout(debounceTimer);
            if (q.length < 1) {
                results.style.display = 'none';
                results.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        results.innerHTML = '';
                        if (data.length === 0) {
                            results.innerHTML = '<div class="list-group-item text-muted">No match found</div>';
                        } else {
                            data.forEach(item => {
                                const el = document.createElement('button');
                                el.type = 'button';
                                el.className = 'list-group-item list-group-item-action';
                                el.textContent = item.name;
                                el.addEventListener('click', () => {
                                    input.value = item.name;
                                    hidden.value = item.id;
                                    results.style.display = 'none';
                                });
                                results.appendChild(el);
                            });
                        }
                        results.style.display = 'block';
                    });
            }, 300); // debounce
        });

        document.addEventListener('click', function (e) {
            if (!results.contains(e.target) && e.target !== input) {
                results.style.display = 'none';
            }
        });
    }

    setupSearchField('ap-patient-search', 'ap-patient-id', 'ap-patient-results', "{{ route('patients.search') }}");
    setupSearchField('ap-doctor-search', 'ap-doctor-id', 'ap-doctor-results', "{{ route('doctors.search') }}");
</script>
@endsection