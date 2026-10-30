@extends('admin.layouts.master')

@section('title', 'Payments - Edit')

@section('content')
    <div class="block block-rounded block-transparent mt-5">
        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Edit Payment"
                subtitle="INV-{{ str_pad($payment->invoice_id, 4, '0', STR_PAD_LEFT) }} - {{ $payment->invoice->patient->name ?? 'N/A' }}">
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to payments
                </a>
            </x-admin.phead>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="amount">Amount (&#2547;)</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount"
                                    value="{{ old('amount', $payment->amount) }}">
                                <x-admin.error-msg name="amount" />
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="payment_method">Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    @php $current = old('payment_method', $payment->payment_method); @endphp
                                    @if (! in_array($current, $methods))
                                        <option value="{{ $current }}" selected>{{ $current }}</option>
                                    @endif
                                    @foreach ($methods as $m)
                                        <option value="{{ $m }}" @selected($current == $m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                                <x-admin.error-msg name="payment_method" />
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label" for="payment_date">Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date"
                                    value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}">
                                <x-admin.error-msg name="payment_date" />
                            </div>
                        </div>
                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Update Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
