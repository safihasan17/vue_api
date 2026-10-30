@extends('admin.layouts.master')

@section('title', 'Supplier Details')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="{{ $supplier->name }}" subtitle="Supplier details">
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Suppliers
                </a>
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>
            </x-admin.phead>

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">Supplier Information</h3></div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <tbody>
                                <tr><td class="fw-semibold" style="width: 200px;">Name</td><td>{{ $supplier->name }}</td></tr>
                                <tr><td class="fw-semibold">Contact</td><td>{{ $supplier->contact ?? '—' }}</td></tr>
                                <tr><td class="fw-semibold">Address</td><td>{{ $supplier->address ?? '—' }}</td></tr>
                                <tr><td class="fw-semibold">Added At</td><td>{{ $supplier->created_at?->format('d M Y, h:i A') ?? '-' }}</td></tr>
                            </tbody>
                        </table>
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
                                    <th class="fs-sm">Medicine</th>
                                    <th class="text-center fs-sm">Qty</th>
                                    <th class="text-center fs-sm">Price</th>
                                    <th class="text-center fs-sm">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $p)
                                    <tr>
                                        <td><a href="{{ route('medicine-purchases.show', $p->id) }}">{{ $p->purchase_date->format('d M Y') }}</a></td>
                                        <td>{{ $p->medicine->name ?? 'N/A' }}</td>
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
