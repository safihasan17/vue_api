@extends('admin.layouts.master')

@section('title', 'users')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="users" subtitle="Mange table from this page">

                <a href="{{ route('users.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add User
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row items-push">
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalUsers }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total System Users</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="hm_roles.html">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-info">{{ $totalRoles }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Roles Defined</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $activeUsers }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Active</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-danger">{{ $inactiveUsers }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Inactive</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">System Users</h3>
                            <div class="block-options">
                                <div class="dropdown">
                                    <button type="button" class="btn-block-option" id="dropdown-filters-users"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Filters <i class="fa fa-angle-down ms-1"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-filters-users">
                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                            href="javascript:void(0)">Active <span
                                                class="badge bg-success rounded-pill">52</span></a>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                            href="javascript:void(0)">Suspended <span
                                                class="badge bg-danger rounded-pill">6</span></a>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                            href="javascript:void(0)">All <span
                                                class="badge bg-primary rounded-pill">58</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Name</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Role</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Email</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Phone</th>
                                            <th class="text-center fs-sm">Status</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($users as $item)
                                            <tr>

                                                <td>
                                                    <div>{{ $item->id }}</div>
                                                    <a class="fw-semibold" href="javascript:void(0)">{{ $item->name }}</a>


                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->role->name }}</td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->email }}</td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->phone }}</td>
                                                <td>
                                                    <span
                                                        class="badge border {{ $item->active == 1 ? 'border-success text-success' : 'border-danger text-danger' }}">
                                                        {{ $item->active == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('users.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('users.edit', ['user' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button" class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            title="Delete row" data-bs-toggle="modal"
                                                            data-bs-target="#modalDelete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach


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

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete User">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you ant to Delete this User</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>

            <hr>

            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" title="Delete row"
                    data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn  btn-danger" title="Delete row"
                    data-bs-dismiss="modal">Delete</button>
            </form>

        </div>
    </x-admin.modal>
@endsection


@section('script')

<script>
document.querySelectorAll('.delete').forEach(button=>{
    button.addEventListener('click', function(){
        let id = this.dataset.id;
        let name = this.dataset.name;
        // alert(id);

        document.querySelector('#modalDelete .name').innerText = name;
        document.querySelector('#modalDelete form').action = `{{ route('users.destroy' , ['user'=>':id']) }}` .replace(':id', id);
        
    })
})
</script>

@endsection
