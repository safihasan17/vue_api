@extends('admin.layouts.master')

@section('title', 'roles - Create')

@section('content')

    <div class="block block-rounded block-transparent mt-5">

        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="roles create" subtitle="Create a role from this section">

                <a href="{{ route('roles.index') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to roles
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
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="role-name">Role Name</label>
                                <input type="text" class="form-control" id="role-name" name="name"
                                    value="{{ old('name') }}" placeholder="e.g. Lab Technician">
                                <x-admin.error-msg name="name" />
                            </div>
                        </div>

                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

@endsection
