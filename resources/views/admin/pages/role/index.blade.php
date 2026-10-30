@extends('admin.layouts.master')

@section('title', 'roles')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="roles" subtitle="Manage user roles from this page">

                <a href="{{ route('roles.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Role
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row items-push">
                <div class="col-6 col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalRoles }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Roles</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $assignedRoles }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Roles In Use</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-warning">{{ $unusedRoles }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Unused Roles</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Roles</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm" style="width: 70px;">ID</th>
                                            <th class="fs-sm">Name</th>
                                            <th class="text-center fs-sm">Users</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($roles as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td class="fw-semibold">
                                                    {{ $item->name }}
                                                    @if ($item->isProtected())
                                                        <span class="badge bg-secondary ms-1">Core</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->users_count }}</td>
                                                <td class="text-center">
<div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('roles.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('roles.edit', $item->id) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        @if (auth()->user()->role_id == 1 && !$item->isProtected())
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded delete"
                                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                                title="Delete" data-bs-toggle="modal"
                                                                data-bs-target="#modalDelete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No roles found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-admin.modal id="modalDelete" title="Delete Role">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p>Are you sure you want to delete this role?</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>

            <hr>

            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </x-admin.modal>
@endsection

@section('script')
    <script>
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#modalDelete .name').innerText = this.dataset.name;
                document.querySelector('#modalDelete form').action =
                    `{{ route('roles.destroy', ['role' => ':id']) }}`.replace(':id', this.dataset.id);
            })
        })
    </script>
@endsection
