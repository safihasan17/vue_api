@extends('admin.layouts.master')

@section('title', 'Lab Test Order Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Lab Test Order"
                subtitle="{{ $labTestOrder->patient->name ?? 'N/A' }} &mdash; {{ $labTestOrder->test->test_name ?? 'N/A' }}">

                <a href="{{ route('lab-test-orders.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Orders
                </a>

                <a href="{{ route('lab-test-orders.edit', $labTestOrder->id) }}" type="button"
                    class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>

                @if (in_array(auth()->user()->role_id, [1, 3]) && !$labTestOrder->prescription_id)
                    <form action="{{ route('lab-test-orders.generate-invoice', $labTestOrder->id) }}" method="POST"
                        class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-file-invoice-dollar opacity-50 me-1"></i> Generate Invoice
                        </button>
                    </form>
                @endif

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @php
                $statusColors = [
                    'Pending' => 'warning',
                    'In Progress' => 'info',
                    'Completed' => 'success',
                    'Cancelled' => 'danger',
                ];
                $statusColor = $statusColors[$labTestOrder->status] ?? 'secondary';
            @endphp

            <div class="row">
                <!-- Left: Summary -->
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <span class="badge bg-{{ $statusColor }} fs-sm mt-3">{{ $labTestOrder->status }}</span>

                            <div class="mt-3">
                                <h5 class="mb-0">{{ $labTestOrder->patient->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">Patient</p>
                            </div>

                            <hr>

                            <div>
                                <h5 class="mb-0">{{ $labTestOrder->doctor->user->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">{{ $labTestOrder->doctor->specialization ?? '-' }}
                                    &mdash; {{ $labTestOrder->doctor->department->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Order Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Patient</td>
                                            <td>{{ $labTestOrder->patient->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Doctor</td>
                                            <td>{{ $labTestOrder->doctor->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Test</td>
                                            <td>{{ $labTestOrder->test->test_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Category</td>
                                            <td>{{ $labTestOrder->test->category ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Price</td>
                                            <td>৳{{ number_format($labTestOrder->test->price ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Order Date</td>
                                            <td>{{ $labTestOrder->order_date?->format('d M Y') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status</td>
                                            <td><span
                                                    class="badge bg-{{ $statusColor }}">{{ $labTestOrder->status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold align-top">Result</td>
                                            <td style="white-space: pre-line;">
                                                {{ $labTestOrder->result ?? 'Not available yet.' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Ordered At</td>
                                            <td>{{ $labTestOrder->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $labTestOrder->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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
