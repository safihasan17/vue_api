@extends('admin.layouts.master')

@section('title', 'Appointment Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Appointment Details"
                subtitle="{{ $appointment->patient->name ?? 'N/A' }} &mdash; {{ optional($appointment->appointment_date)->format('d M Y') ?? '-' }}">

                <a href="{{ route('appointments.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Appointments
                </a>

                <a href="{{ route('appointments.edit', $appointment->id) }}" type="button" class="btn btn-sm btn-primary">
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
                <!-- Left: Summary -->
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            @php
                                $statusColors = [
                                    'Scheduled' => 'info',
                                    'Checked-in' => 'success',
                                    'Waiting' => 'warning',
                                    'Completed' => 'secondary',
                                    'Cancelled' => 'danger',
                                ];
                                $statusColor = $statusColors[$appointment->status] ?? 'secondary';
                            @endphp
                            <i class="fa fa-calendar-check fs-1 text-{{ $statusColor }} mt-3"></i>

                            <div class="mt-3">
                                <h4 class="mb-0">
                                    <a href="{{ route('prescriptions.create', ['appointment_id' => $appointment->id]) }}"
                                        class="text-body" title="Add prescription for this patient">
                                        {{ $appointment->patient->name ?? 'N/A' }}
                                    </a>
                                </h4>
                                <p class="text-muted mb-1">with {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
                                <span class="badge bg-{{ $statusColor }}">{{ $appointment->status }}</span>
                            </div>

                        </div>
                    </div>

                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Doctor</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <ul class="list-unstyled fs-sm mb-0">
                                <li class="mb-3">
                                    <div class="fw-semibold">{{ $appointment->doctor->user->name ?? 'N/A' }}</div>
                                    <div class="text-muted">{{ $appointment->doctor->department->name ?? '-' }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a class="btn btn-alt-primary w-100 mb-2"
                        href="{{ route('patients.show', $appointment->patient_id) }}">
                        <i class="fa fa-user me-1"></i> View Patient Profile
                    </a>
                    @if (auth()->user()->role_id == 2 || 1)
                        <a class="btn btn-primary w-100"
                            href="{{ route('prescriptions.create', ['appointment_id' => $appointment->id]) }}">
                            <i class="fa fa-file-prescription me-1"></i> Add Prescription
                        </a>
                    @endif

                    @if (in_array(auth()->user()->role_id, [1, 3]))
                        <form action="{{ route('appointments.generate-invoice', $appointment->id) }}" method="POST"
                            class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa fa-file-invoice-dollar me-1"></i> Doctor Fee Invoice
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Appointment Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Patient</td>
                                            <td>
                                                <a>
                                                    {{ $appointment->patient->name ?? 'N/A' }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Doctor</td>
                                            <td>{{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Department</td>
                                            <td>{{ $appointment->doctor->department->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Date</td>
                                            <td>{{ optional($appointment->appointment_date)->format('d M Y') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Time</td>
                                            <td>
                                                {{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status</td>
                                            <td><span
                                                    class="badge bg-{{ $statusColor }}">{{ $appointment->status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Reason for Visit</td>
                                            <td>{{ $appointment->reason ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Booked At</td>
                                            <td>{{ $appointment->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $appointment->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Page Content -->
    </main>

@endsection
