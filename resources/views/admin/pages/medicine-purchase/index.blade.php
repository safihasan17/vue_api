@extends('admin.layouts.master')

@section('title', 'Medicine Purchases')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="Medicine Purchases" subtitle="Purchases from suppliers (each purchase adds to stock)">
                <a href="{{ route('medicine-purchases.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> New Purchase
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
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-primary">{{ $totalPurchases }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Total Purchases</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-info">{{ number_format($totalUnits) }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Units Purchased</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-success">&#2547;{{ number_format($monthSpent, 2) }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Spent This Month</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-warning">&#2547;{{ number_format($totalSpent, 2) }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Total Spent</p></div>
                    </a>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">All Purchases</h3></div>
                <div class="block-content block-content-full">
                    <form method="GET" action="{{ route('medicine-purchases.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search medicine name">
                        </div>
                        <div class="col-md-4">
                            <select name="supplier_id" class="form-select form-select-sm">
                                <option value="">All suppliers</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-1">
                            <button class="btn btn-sm btn-primary w-100" type="submit">Filter</button>
                            <a href="{{ route('medicine-purchases.index') }}" class="btn btn-sm btn-alt-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Date</th>
                                    <th class="fs-sm">Medicine</th>
                                    <th class="d-none d-sm-table-cell text-center fs-sm">Supplier</th>
                                    <th class="text-center fs-sm">Qty</th>
                                    <th class="d-none d-md-table-cell text-center fs-sm">Unit Price</th>
                                    <th class="text-center fs-sm">Total</th>
                                    <th class="text-center fs-sm" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $item)
                                    <tr>
                                        <td><a class="fw-semibold" href="{{ route('medicine-purchases.show', $item->id) }}">{{ $item->purchase_date->format('d M Y') }}</a></td>
                                        <td>{{ $item->medicine->name ?? 'N/A' }}</td>
                                        <td class="d-none d-sm-table-cell text-center">{{ $item->supplier->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="d-none d-md-table-cell text-center">&#2547;{{ number_format($item->purchase_price, 2) }}</td>
                                        <td class="text-center fw-semibold">&#2547;{{ number_format($item->total_cost, 2) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('medicine-purchases.show', $item->id) }}" class="btn btn-sm btn-outline-primary rounded" title="View"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('medicine-purchases.edit', $item->id) }}" class="btn btn-sm btn-outline-warning rounded" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->medicine->name ?? 'purchase' }} ({{ $item->quantity }})" title="Delete row"
                                                    data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">No purchases found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer-control">
                        {{ $purchases->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-admin.modal id="modalDelete" title="Delete Purchase">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p>Delete this purchase? Its quantity will be removed from stock.</p>
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
                    `{{ route('medicine-purchases.destroy', 'ID_PLACEHOLDER') }}`.replace('ID_PLACEHOLDER', this.dataset.id);
            })
        })
    </script>
@endsection
