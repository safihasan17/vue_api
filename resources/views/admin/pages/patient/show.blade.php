@extends('admin.layouts.master')

@section('title', 'Patient Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Patient Record" subtitle="{{ $patient->name }}">

                <a href="{{ route('patients.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Patients
                </a>

                <a href="{{ route('patients.edit', $patient->id) }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Left: Profile Summary -->
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <img class="img-avatar img-avatar96 img-avatar-thumb mt-3"
                                src="{{ $patient->avatar ?? asset('media/avatars/avatar5.jpg') }}"
                                alt="{{ $patient->name }}">

                            <div class="mt-3">
                                <h4 class="mb-0">{{ $patient->name }}</h4>
                                <p class="text-muted mb-0">{{ $patient->gender ?? '-' }}</p>
                            </div>

                        </div>
                    </div>

                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Recent Visits</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <ul class="list-unstyled fs-sm mb-0">
                                @forelse ($patient->visits ?? [] as $visit)
                                    <li class="mb-3">
                                        <div class="fw-semibold">{{ $visit->title }}</div>
                                        <div class="text-muted">
                                            {{ $visit->visited_at?->format('d M Y') }} &mdash;
                                            {{ $visit->doctor_name ?? $visit->note }}
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted">No visits recorded yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    {{-- <a class="btn btn-alt-primary w-100" href="{{ route('appointments.create', ['patient' => $patient->id]) }}">
                        <i class="fa fa-calendar-plus me-1"></i> Book New Appointment
                    </a> --}}
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Personal &amp; Contact Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Full Name</td>
                                            <td>{{ $patient->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Date of Birth</td>
                                            <td>
                                                {{ optional($patient->dob)->format('d M Y') ?? '-' }}
                                                @if ($patient->dob)
                                                    ({{ $patient->dob->age }} yrs)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Gender</td>
                                            <td>{{ $patient->gender ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Blood Group</td>
                                            <td>{{ $patient->blood_group ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Phone</td>
                                            <td>{{ $patient->phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Emergency Contact</td>
                                            <td>{{ $patient->emergency_contact ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Address</td>
                                            <td>{{ $patient->address ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Guardian Name</td>
                                            <td>{{ $patient->guardian_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Registered At</td>
                                            <td>{{ $patient->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $patient->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                 
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Medical History</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th class="fs-sm">Condition / Disease</th>
                                        <th class="fs-sm">Allergy</th>
                                        <th class="fs-sm">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($patient->medicalHistories ?? [] as $history)
                                        <tr>
                                            <td>{{ $history->condition }}</td>
                                            <td>{{ $history->allergy ?? '&mdash;' }}</td>
                                            <td class="fs-sm text-muted">{{ $history->notes }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No medical history
                                                recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Page Content -->
    </main>

@endsection
