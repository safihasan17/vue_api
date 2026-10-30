@extends('admin.layouts.master')

@section('title', 'Suppliers')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="Suppliers" subtitle="Manage medicine suppliers">
                <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Supplier
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
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalSuppliers }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Suppliers</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">All Suppliers</h3></div>
                <div class="block-content block-content-full">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 mb-3">
                        <div class="col-md-6">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                                placeholder="Search name or contact">
                        </div>
                        <div class="col-md-3 d-flex gap-1">
                            <button class="btn btn-sm btn-primary w-100" type="submit">Search</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-alt-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Name</th>
                                    <th class="d-none d-sm-table-cell text-center fs-sm">Contact</th>
                                    <th class="d-none d-md-table-cell fs-sm">Address</th>
                                    <th class="text-center fs-sm">Purchases</th>
                                    <th class="text-center fs-sm" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $item)
                                    <tr>
                                        <td><a class="fw-semibold" href="{{ route('suppliers.show', $item->id) }}">{{ $item->name }}</a></td>
                                        <td class="d-none d-sm-table-cell text-center">{{ $item->contact ?? '—' }}</td>
                                        <td class="d-none d-md-table-cell">{{ \Illuminate\Support\Str::limit($item->address, 50) ?: '—' }}</td>
                                        <td class="text-center">{{ $item->purchases_count }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('suppliers.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded" title="View"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('suppliers.edit', $item->id) }}" class="btn btn-sm btn-outline-warning rounded" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Delete row"
                                                    data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No suppliers found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer-control">
                        {{ $suppliers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-admin.modal id="modalDelete" title="Delete Supplier">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p>Are you sure you want to delete this supplier?</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>
            <hr>
            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
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
                    `{{ route('suppliers.destroy', 'ID_PLACEHOLDER') }}`.replace('ID_PLACEHOLDER', this.dataset.id);
            })
        })
    </script>
@endsection
