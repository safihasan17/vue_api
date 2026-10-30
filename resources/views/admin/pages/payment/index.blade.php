@extends('admin.layouts.master')

@section('title', 'Payments')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="Payments" subtitle="All payments received (counter and online)">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-file-invoice-dollar opacity-50 me-1"></i> Invoices
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
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-success">&#2547;{{ number_format($totalCollected, 2) }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Total Collected</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-primary">&#2547;{{ number_format($monthCollected, 2) }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Collected This Month</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('payments.index', ['status' => 'Success', 'method' => 'SSLCommerz']) }}">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-info">{{ $onlineCount }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Online Payments</p></div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="{{ route('payments.index', ['status' => 'Pending']) }}">
                        <div class="block-content block-content-full"><div class="fs-2 fw-semibold text-warning">{{ $pendingCount }}</div></div>
                        <div class="block-content py-2 bg-body-light"><p class="fw-medium fs-sm text-muted mb-0">Pending Online</p></div>
                    </a>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">All Payments</h3></div>
                <div class="block-content block-content-full">
                    <form method="GET" action="{{ route('payments.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                                placeholder="Patient, invoice no. or transaction ID">
                        </div>
                        <div class="col-md-3">
                            <select name="method" class="form-select form-select-sm">
                                <option value="">All methods</option>
                                @foreach ($methods as $m)
                                    <option value="{{ $m }}" @selected(request('method') == $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All statuses</option>
                                @foreach (['Success', 'Pending', 'Failed', 'Cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(request('status') == $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button class="btn btn-sm btn-primary w-100" type="submit">Filter</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-alt-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Date</th>
                                    <th class="fs-sm">Invoice</th>
                                    <th class="d-none d-sm-table-cell fs-sm">Patient</th>
                                    <th class="text-center fs-sm">Method</th>
                                    <th class="text-center fs-sm">Amount</th>
                                    <th class="d-none d-md-table-cell text-center fs-sm">Received By</th>
                                    <th class="text-center fs-sm">Status</th>
                                    <th class="text-center fs-sm" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $item)
                                    <tr>
                                        <td>{{ $item->payment_date?->format('d M Y') }}</td>
                                        <td><a class="fw-semibold" href="{{ route('invoices.show', $item->invoice_id) }}">INV-{{ str_pad($item->invoice_id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                        <td class="d-none d-sm-table-cell">{{ $item->invoice->patient->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->payment_method }}</td>
                                        <td class="text-center fw-semibold">&#2547;{{ number_format($item->amount, 2) }}</td>
                                        <td class="d-none d-md-table-cell text-center">{{ $item->receiver->name ?? '—' }}</td>
                                        <td class="text-center"><span class="badge bg-{{ $item->status_color }}">{{ $item->status }}</span></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @unless ($item->isGateway())
                                                    <a href="{{ route('payments.edit', $item->id) }}" class="btn btn-sm btn-outline-warning rounded" title="Edit"><i class="fa fa-pencil-alt"></i></a>
                                                @endunless
                                                <form action="{{ route('payments.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this payment? The invoice will be recalculated.{{ $item->isGateway() && $item->status === 'Success' ? ' This does NOT refund the customer at the gateway.' : '' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded" title="Delete"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted">No payments found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer-control">
                        {{ $payments->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
