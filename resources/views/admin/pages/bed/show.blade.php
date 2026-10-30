@extends('admin.layouts.master')

@section('title', 'Bed Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Bed {{ $bed->bed_number }}" subtitle="{{ $bed->ward->name ?? 'N/A' }}">

                <a href="{{ route('beds.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Beds
                </a>

                <a href="{{ route('beds.edit', $bed->id) }}" type="button" class="btn btn-sm btn-primary">
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
                    'Available' => 'success',
                    'Occupied' => 'danger',
                    'Reserved' => 'info',
                    'Maintenance' => 'warning',
                ];
                $statusColor = $statusColors[$bed->status] ?? 'secondary';
            @endphp

            <div class="row">
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <i class="fa fa-bed fs-1 text-{{ $statusColor }} mt-3"></i>

                            <div class="mt-3">
                                <h4 class="mb-0">{{ $bed->bed_number }}</h4>
                                <p class="text-muted mb-1">{{ $bed->ward->name ?? 'N/A' }}</p>
                                <span class="badge bg-{{ $statusColor }}">{{ $bed->status }}</span>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Bed Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Bed Number</td>
                                            <td>{{ $bed->bed_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Ward</td>
                                            <td>{{ $bed->ward->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status</td>
                                            <td><span class="badge bg-{{ $statusColor }}">{{ $bed->status }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Added At</td>
                                            <td>{{ $bed->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $bed->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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
