@extends('admin.layouts.master')

@section('title', 'Invoice Item Details')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Invoice Item" subtitle="{{ $invoiceItem->description }}">

                <a href="{{ route('invoice-items.index') }}" type="button" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Invoice Items
                </a>

                <a href="{{ route('invoice-items.edit', $invoiceItem->id) }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Item Information</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold" style="width: 200px;">Invoice</td>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoiceItem->invoice_id) }}">
                                                    INV-{{ str_pad($invoiceItem->invoice_id, 4, '0', STR_PAD_LEFT) }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Patient</td>
                                            <td>{{ $invoiceItem->invoice->patient->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Item Type</td>
                                            <td><span class="badge bg-secondary">{{ $invoiceItem->item_type }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Description</td>
                                            <td>{{ $invoiceItem->description }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Amount</td>
                                            <td>৳{{ number_format($invoiceItem->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Added At</td>
                                            <td>{{ $invoiceItem->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Updated</td>
                                            <td>{{ $invoiceItem->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
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

@endsection