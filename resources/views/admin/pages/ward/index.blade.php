@extends('admin.layouts.master')

@section('title', 'wards')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="wards" subtitle="Manage wards from this page">

                <a href="{{ route('wards.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Ward
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row items-push">
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalWards }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Wards</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-info">{{ $totalFloors }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Floors Covered</p>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $totalTypes }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Ward Types</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Wards</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Name</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Floor</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Type</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($wards as $item)
                                            <tr>

                                                <td>
                                                    <div>{{ $item->id }}</div>
                                                    <a class="fw-semibold"
                                                        href="{{ route('wards.show', $item->id) }}">{{ $item->name }}</a>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->floor }}</td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->type }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('wards.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('wards.edit', ['ward' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            title="Delete row" data-bs-toggle="modal"
                                                            data-bs-target="#modalDelete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No wards found.</td>
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
        <!-- END Page Content -->
    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Ward">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to delete this ward?</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>

            <hr>

            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" title="Cancel"
                    data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn btn-danger" title="Delete row">Delete</button>
            </form>

        </div>
    </x-admin.modal>
@endsection


@section('script')

    <script>
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.dataset.id;
                let name = this.dataset.name;

                document.querySelector('#modalDelete .name').innerText = name;
                document.querySelector('#modalDelete form').action = `{{ route('wards.destroy', ['ward' => ':id']) }}`
                    .replace(':id', id);

            })
        })
    </script>

@endsection
