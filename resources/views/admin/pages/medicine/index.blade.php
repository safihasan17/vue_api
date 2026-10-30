@extends('admin.layouts.master')

@section('title', 'Medicines')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="Pharmacy / Medicines" subtitle="Manage medicines and monitor stock levels">
                <a href="{{ route('medicine-purchases.create') }}" class="btn btn-sm btn-alt-primary">
                    <i class="fa fa-truck opacity-50 me-1"></i> New Purchase
                </a>
                <a href="{{ route('medicines.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Medicine
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
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('medicines.index') }}">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalMedicines }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Medicines</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('medicines.index', ['stock' => 'in']) }}">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $inStockCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">In Stock</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('medicines.index', ['stock' => 'low']) }}">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-warning">{{ $lowStockCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Low Stock (&lt; {{ $low }})</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('medicines.index', ['stock' => 'out']) }}">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-danger">{{ $outOfStockCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Out of Stock</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">All Medicines</h3>
                </div>
                <div class="block-content block-content-full">
                    <form method="GET" action="{{ route('medicines.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                                placeholder="Search name or generic name">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select form-select-sm">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected(request('category') == $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="stock" class="form-select form-select-sm">
                                <option value="">All stock levels</option>
                                <option value="in" @selected(request('stock') == 'in')>In Stock</option>
                                <option value="low" @selected(request('stock') == 'low')>Low Stock (&lt; {{ $low }})</option>
                                <option value="out" @selected(request('stock') == 'out')>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button class="btn btn-sm btn-primary w-100" type="submit">Filter</button>
                            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-alt-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Name</th>
                                    <th class="d-none d-sm-table-cell text-center fs-sm">Generic Name</th>
                                    <th class="d-none d-md-table-cell text-center fs-sm">Category</th>
                                    <th class="text-center fs-sm">Unit Price</th>
                                    <th class="text-center fs-sm">Stock</th>
                                    <th class="text-center fs-sm">Status</th>
                                    <th class="text-center fs-sm" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($medicines as $item)
                                    <tr>
                                        <td><a class="fw-semibold" href="{{ route('medicines.show', $item->id) }}">{{ $item->name }}</a></td>
                                        <td class="d-none d-sm-table-cell text-center">{{ $item->generic_name ?? '—' }}</td>
                                        <td class="d-none d-md-table-cell text-center">{{ $item->category ?? '—' }}</td>
                                        <td class="text-center">{{ $item->unit_price !== null ? '৳' . number_format($item->unit_price, 2) : '—' }}</td>
                                        <td class="text-center fw-semibold">{{ $item->stock_quantity }}</td>
                                        <td class="text-center"><span class="badge bg-{{ $item->stock_color }}">{{ $item->stock_status }}</span></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('medicines.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded" title="View"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('medicines.edit', $item->id) }}" class="btn btn-sm btn-outline-warning rounded" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Delete row"
                                                    data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">No medicines found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer-control">
                        {{ $medicines->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-admin.modal id="modalDelete" title="Delete Medicine">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p>Are you sure you want to delete this medicine?</p>
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
                    `{{ route('medicines.destroy', 'ID_PLACEHOLDER') }}`.replace('ID_PLACEHOLDER', this.dataset.id);
            })
        })
    </script>
@endsection
