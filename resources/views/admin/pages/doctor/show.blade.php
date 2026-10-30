@extends('admin.layouts.master')

@section('title', 'Doctor Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Doctor Profile" subtitle="{{ $doctor->user->name ?? 'N/A' }}">

                <a href="{{ route('doctors.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Doctors
                </a>

                <a href="{{ route('doctors.edit', $doctor->id) }}" type="button" class="btn btn-sm btn-primary">
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
               
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            @if ($doctor->image)
                                <img src="{{ asset($doctor->image) }}" alt="" class="rounded-3" width="200"
                                    height="200">
                            @else
                                <img src="https://placehold.net/product-400x400.png" alt="" class="rounded-3"
                                    width="60" height="60">
                            @endif

                            <div class="mt-3">
                                <h4 class="mb-0">{{ $doctor->user->name ?? 'N/A' }}</h4>
                                <p class="text-muted mb-0">{{ $doctor->qualification ?? '-' }}</p>
                            </div>

                        </div>
                    </div>

                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Recent Appointments</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <ul class="list-unstyled fs-sm mb-0">
                                @forelse ($doctor->appointments ?? [] as $appointment)
                                    <li class="mb-3">
                                        <div class="fw-semibold">{{ $appointment->patient->name ?? '' }}</div>
                                        <div class="text-muted">
                                            {{ $appointment->scheduled_at?->format('d M Y') }}
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted">No appointments recorded yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Professional Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Full Name</td>
                                            <td>{{ $doctor->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Email</td>
                                            <td>{{ $doctor->user->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Phone</td>
                                            <td>{{ $doctor->user->phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Department</td>
                                            <td>{{ $doctor->department->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Specialization</td>
                                            <td>{{ $doctor->specialization ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Qualification</td>
                                            <td>{{ $doctor->qualification ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Consultation Fee</td>
                                            <td>৳{{ number_format($doctor->consultation_fee ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Registered At</td>
                                            <td>{{ $doctor->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $doctor->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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
