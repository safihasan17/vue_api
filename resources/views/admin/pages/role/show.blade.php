@extends('admin.layouts.master')

@section('title', 'role details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Role Details" subtitle="View role and the users who have it">

                <a href="{{ route('roles.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Roles
                </a>

            </x-admin.phead>

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        {{ $role->name }}
                        @if ($role->isProtected())
                            <span class="badge bg-secondary ms-1">Core</span>
                        @endif
                    </h3>
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fa fa-pencil-alt me-1"></i> Edit
                    </a>
                </div>
                <div class="block-content block-content-full">
                    <p class="mb-1"><span class="text-muted">ID:</span> {{ $role->id }}</p>
                    <p class="mb-3"><span class="text-muted">Users with this role:</span> {{ $role->users->count() }}</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Name</th>
                                    <th class="fs-sm">Email</th>
                                    <th class="text-center fs-sm">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($role->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            @if ($user->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No users have this role.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
