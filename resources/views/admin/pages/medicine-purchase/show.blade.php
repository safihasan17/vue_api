@extends('admin.layouts.master')

@section('title', 'Purchase Details')

@section('content')
    <main id="main-container">
        <div class="content">
            <x-admin.phead title="Purchase #{{ $purchase->id }}" subtitle="{{ $purchase->purchase_date->format('d M Y') }}">
                <a href="{{ route('medicine-purchases.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Purchases
                </a>
                <a href="{{ route('medicine-purchases.edit', $purchase->id) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>
            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="block block-rounded">
                <div class="block-header block-header-default"><h3 class="block-title">Purchase Information</h3></div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <tbody>
                                <tr><td class="fw-semibold" style="width: 200px;">Medicine</td>
                                    <td>@if ($purchase->medicine)<a href="{{ route('medicines.show', $purchase->medicine_id) }}">{{ $purchase->medicine->name }}</a>@else N/A @endif</td></tr>
                                <tr><td class="fw-semibold">Supplier</td>
                                    <td>@if ($purchase->supplier)<a href="{{ route('suppliers.show', $purchase->supplier_id) }}">{{ $purchase->supplier->name }}</a>@else N/A @endif</td></tr>
                                <tr><td class="fw-semibold">Quantity</td><td>{{ $purchase->quantity }}</td></tr>
                                <tr><td class="fw-semibold">Unit Price</td><td>&#2547;{{ number_format($purchase->purchase_price, 2) }}</td></tr>
                                <tr><td class="fw-semibold">Total Cost</td><td class="fw-bold">&#2547;{{ number_format($purchase->total_cost, 2) }}</td></tr>
                                <tr><td class="fw-semibold">Purchase Date</td><td>{{ $purchase->purchase_date->format('d M Y') }}</td></tr>
                                <tr><td class="fw-semibold">Recorded At</td><td>{{ $purchase->created_at?->format('d M Y, h:i A') ?? '-' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
