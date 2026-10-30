@extends('admin.layouts.master')

 @section('title', 'Invoice Items - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Add Invoice Item" subtitle="Add a line item to an invoice">

                 <a href="{{ route('invoice-items.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to invoice items
                 </a>

             </x-admin.phead>

             @if (session('error'))
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     {{ session('error') }}
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                 </div>
             @endif


             <div class="card mt-3">
                 <div class="card-body">
                     <form action="{{ route('invoice-items.store') }}" method="POST">
                         @csrf
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ii-invoice">Invoice</label>
                                 <select class="form-select" name="invoice_id">
                                     <option value="" selected disabled>Select Invoice</option>
                                     @foreach ($invoices as $invoice)
                                         <option value="{{ $invoice->id }}" @selected(old('invoice_id') == $invoice->id)>
                                             INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }} &mdash;
                                             {{ $invoice->patient->name ?? 'N/A' }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="invoice_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ii-type">Item Type</label>
                                 <select class="form-select" name="item_type">
                                     <option value="" selected disabled>Select Type</option>
                                     @foreach ($itemTypes as $type)
                                         <option value="{{ $type }}" @selected(old('item_type') == $type)>{{ $type }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="item_type" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-8 mb-4">
                                 <label class="form-label" for="ii-description">Description</label>
                                 <input type="text" class="form-control" name="description"
                                     value="{{ old('description') }}" placeholder="e.g. Doctor consultation fee">
                                 <x-admin.error-msg name="description" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ii-amount">Amount (&#2547;)</label>
                                 <input type="number" step="0.01" min="0" class="form-control" name="amount"
                                     value="{{ old('amount') }}" placeholder="e.g. 500">
                                 <x-admin.error-msg name="amount" />
                             </div>
                         </div>

                         <div class="fs-sm text-muted mb-3">
                             Saving this item will automatically update the invoice's total amount.
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('invoice-items.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Item</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection