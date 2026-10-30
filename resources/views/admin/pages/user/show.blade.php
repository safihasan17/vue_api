@extends('admin.layouts.master')

@section('title', 'user details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="User Details" subtitle="View individual user information">

                <a href="{{ route('users.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Users
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
                                src="{{ $user->avatar ?? asset('media/avatars/avatar10.jpg') }}"
                                alt="{{ $user->name }}">

                            <div class="mt-3">
                                <h4 class="mb-0">{{ $user->name }}</h4>
                                <p class="text-muted mb-2">{{ $user->role->name ?? '-' }}</p>

                                @if ($user->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>

                            

                           
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="col-lg-8">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Account Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">User ID</td>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Full Name</td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Role</td>
                                            <td>{{ $user->role->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Email</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Phone</td>
                                            <td>{{ $user->phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status</td>
                                            <td>
                                                @if ($user->active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Joined At</td>
                                            <td>{{ $user->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $user->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
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