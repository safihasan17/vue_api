@extends('admin.layouts.master')

@section('title', 'ward details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Ward Details" subtitle="View individual ward information">

                <a href="{{ route('wards.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Wards
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Left: Ward Summary -->
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <div class="mt-3">
                                <i class="fa fa-procedures fa-3x text-primary"></i>
                            </div>

                            <div class="mt-3">
                                <h4 class="mb-0">{{ $ward->name }}</h4>
                                <p class="text-muted mb-2">{{ $ward->floor ?? '-' }}</p>

                                <span class="badge bg-info">{{ $ward->type ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Ward Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Ward ID</td>
                                            <td>{{ $ward->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Ward Name</td>
                                            <td>{{ $ward->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Floor</td>
                                            <td>{{ $ward->floor ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Type</td>
                                            <td>{{ $ward->type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Created At</td>
                                            <td>{{ $ward->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $ward->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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
