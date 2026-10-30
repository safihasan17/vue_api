@extends('admin.layouts.master')

@section('title', 'Admission Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Admission Details"
                subtitle="{{ $admission->patient->name ?? 'N/A' }} &mdash; {{ $admission->ward->name ?? '-' }} / {{ $admission->bed->bed_number ?? '-' }}">

                <a href="{{ route('admissions.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Admissions
                </a>

                <a href="{{ route('admissions.edit', $admission->id) }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @php
                $statusColors = [
                    'Admitted' => 'danger',
                    'Discharged' => 'success',
                    'Transferred' => 'info',
                ];
                $statusColor = $statusColors[$admission->status] ?? 'secondary';
            @endphp

            <div class="row">
                <!-- Left: Summary -->
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <i class="fa fa-procedures fs-1 text-{{ $statusColor }} mt-3"></i>

                            <div class="mt-3">
                                <h5 class="mb-0">{{ $admission->patient->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-1">under {{ $admission->doctor->user->name ?? 'N/A' }}</p>
                                <span class="badge bg-{{ $statusColor }}">{{ $admission->status }}</span>
                            </div>

                        </div>
                    </div>

                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Ward &amp; Bed</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <ul class="list-unstyled fs-sm mb-0">
                                <li class="mb-2"><span class="fw-semibold">Ward:</span> {{ $admission->ward->name ?? '-' }}</li>
                                <li><span class="fw-semibold">Bed:</span> {{ $admission->bed->bed_number ?? '-' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Admission Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Patient</td>
                                            <td>{{ $admission->patient->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Doctor</td>
                                            <td>{{ $admission->doctor->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Department</td>
                                            <td>{{ $admission->doctor->department->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Ward</td>
                                            <td>{{ $admission->ward->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Bed</td>
                                            <td>{{ $admission->bed->bed_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Admission Date</td>
                                            <td>{{ $admission->admission_date?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Discharge Date</td>
                                            <td>{{ $admission->discharge_date?->format('d M Y, h:i A') ?? 'Not discharged yet' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status</td>
                                            <td><span class="badge bg-{{ $statusColor }}">{{ $admission->status }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Recorded At</td>
                                            <td>{{ $admission->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $admission->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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
