@extends('admin.layouts.master')

@section('title', 'Medicine Details')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="{{ $medicine->name }}" subtitle="{{ $medicine->generic_name ?? 'Medicine details' }}">
                <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Medicines
                </a>
                <a href="{{ route('medicines.edit', $medicine->id) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>
            </x-admin.phead>

            <div class="row">
                <div class="col-lg-4">
                    <div class="block block-rounded text-center">
                        <div class="block-content block-content-full">
                            <i class="fa fa-pills fs-1 text-{{ $medicine->stock_color }} mt-3"></i>
                            <div class="mt-3">
                                <h4 class="mb-0">{{ $medicine->name }}</h4>
                                <p class="text-muted mb-1">{{ $medicine->category ?? 'Uncategorised' }}</p>
                                <div class="fs-2 fw-semibold">{{ $medicine->stock_quantity }}</div>
                                <span class="badge bg-{{ $medicine->stock_color }}">{{ $medicine->stock_status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default"><h3 class="block-title">Medicine Information</h3></div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr><td class="fw-semibold" style="width: 200px;">Name</td><td>{{ $medicine->name }}</td></tr>
                                        <tr><td class="fw-semibold">Generic Name</td><td>{{ $medicine->generic_name ?? '—' }}</td></tr>
                                        <tr><td class="fw-semibold">Category</td><td>{{ $medicine->category ?? '—' }}</td></tr>
                                        <tr><td class="fw-semibold">Unit Price</td><td>{{ $medicine->unit_price !== null ? '৳' . number_format($medicine->unit_price, 2) : '—' }}</td></tr>
                                        <tr><td class="fw-semibold">Stock Quantity</td><td>{{ $medicine->stock_quantity }}</td></tr>
                                        <tr><td class="fw-semibold">Added At</td><td>{{ $medicine->created_at?->format('d M Y, h:i A') ?? '-' }}</td></tr>
                                        <tr><td class="fw-semibold">Last Updated</td><td>{{ $medicine->updated_at?->format('d M Y, h:i A') ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">Recent Purchases</h3></div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Date</th>
                                    <th class="fs-sm">Supplier</th>
                                    <th class="text-center fs-sm">Qty</th>
                                    <th class="text-center fs-sm">Price</th>
                                    <th class="text-center fs-sm">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $p)
                                    <tr>
                                        <td><a href="{{ route('medicine-purchases.show', $p->id) }}">{{ $p->purchase_date->format('d M Y') }}</a></td>
                                        <td>{{ $p->supplier->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $p->quantity }}</td>
                                        <td class="text-center">৳{{ number_format($p->purchase_price, 2) }}</td>
                                        <td class="text-center">৳{{ number_format($p->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No purchases yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
