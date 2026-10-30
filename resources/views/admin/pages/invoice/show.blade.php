@extends('admin.layouts.master')

@section('title', 'Invoice Details')

@section('content')
    <main id="main-container">

        <div class="content" id="inv-print-area">
            <x-admin.phead title="Invoice INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}"
                subtitle="{{ $invoice->patient->name ?? 'N/A' }} &mdash; {{ $invoice->invoice_date?->format('d M Y') }}">

                <a href="{{ route('invoices.index') }}" type="button" class="btn btn-sm btn-secondary no-print">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Invoices
                </a>

                <a href="{{ route('invoices.edit', $invoice->id) }}" type="button" class="btn btn-sm btn-primary no-print">
                    <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                </a>

                <button type="button" class="btn btn-sm btn-alt-secondary no-print" onclick="window.print()">
                    <i class="fa fa-print opacity-50 me-1"></i> Print
                </button>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @php
                $payMessages = [
                    'success' => ['success', 'Online payment received successfully.'],
                    'unverified' => [
                        'warning',
                        'Payment could not be verified yet. If money was deducted, check the Payments list shortly.',
                    ],
                    'failed' => ['danger', 'Online payment failed.'],
                    'cancelled' => ['secondary', 'Online payment was cancelled.'],
                ];
                $payFlag = $payMessages[request('pay')] ?? null;
            @endphp
            @if ($payFlag)
                <div class="alert alert-{{ $payFlag[0] }} alert-dismissible fade show no-print" role="alert">
                    {{ $payFlag[1] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            @php
                $statusColors = [
                    'Unpaid' => 'danger',
                    'Partially Paid' => 'warning',
                    'Paid' => 'success',
                    'Cancelled' => 'secondary',
                ];
                $statusColor = $statusColors[$invoice->status] ?? 'secondary';
                $dueAmount = $invoice->total_amount - $invoice->paid_amount;

                $referredDoctor =
                    $invoice->admission->doctor->user->name ?? ($invoice->appointment->doctor->user->name ?? null);
                $patientAge = $invoice->patient->dob ? $invoice->patient->dob->age : null;
            @endphp

            <div class="block block-rounded">
                <div class="block-content block-content-full" style="max-width: 820px; margin: 0 auto;">

                    {{-- Letterhead — printable only --}}
                    <div class="d-none d-print-block text-center mb-4">
                        <h3 class="mb-0">{{ config('app.name', 'Hospital Management') }}</h3>
                        <p class="text-muted mb-0">House/Address line, City &mdash; Phone: 01XXXXXXXXX</p>
                        <hr>
                    </div>

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h4 class="mb-0">Invoice</h4>
                        <div class="text-end">
                            <div class="fw-semibold">Invoice No: INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="fs-sm text-muted">Date: {{ $invoice->invoice_date?->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold" style="width: 110px;">Name</td>
                                        <td>: {{ $invoice->patient->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Age</td>
                                        <td>: {{ $patientAge ? $patientAge . ' year(s)' : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Gender</td>
                                        <td>: {{ $invoice->patient->gender ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Phone</td>
                                        <td>: {{ $invoice->patient->phone ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold" style="width: 110px;">Ref. by</td>
                                        <td>: {{ $referredDoctor ? 'Dr. ' . $referredDoctor : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Type</td>
                                        <td>:
                                            @if ($invoice->admission_id)
                                                IPD (Admission #{{ $invoice->admission_id }})
                                            @elseif ($invoice->appointment_id)
                                                OPD (Appointment #{{ $invoice->appointment_id }})
                                            @else
                                                &mdash;
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">SL</th>
                                    <th>Description</th>
                                    <th class="text-center" style="width: 100px;">Type</th>
                                    <th class="text-end" style="width: 140px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoice->items as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->item_type }}</span>
                                        </td>
                                        <td class="text-end">৳{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items added to this invoice
                                            yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Total Amount</td>
                                        <td class="text-end">৳{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Received</td>
                                        <td class="text-end">৳{{ number_format($invoice->paid_amount, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bold">Due</td>
                                        <td class="text-end fw-bold text-danger">৳{{ number_format($dueAmount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mt-5 pt-5">
                        <div>
                            @if ($invoice->status === 'Paid')
                                <span class="badge bg-success fs-5 px-4 py-2 border border-success">PAID</span>
                            @else
                                <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">{{ $invoice->status }}</span>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="mb-0">____________________________</p>
                            <p class="mb-0 fs-sm">Authorized Signature</p>
                        </div>
                    </div>

                    <div class="d-none d-print-block text-center fs-sm text-muted mt-4">
                        <hr>
                        <p class="mb-0">Thank you for choosing {{ config('app.name', 'our hospital') }}.</p>
                    </div>

                </div>
            </div>

            {{-- ================= PAYMENTS (not printed) ================= --}}
            <div class="block block-rounded no-print">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Payments</h3>
                </div>
                <div class="block-content block-content-full">

                    @if ($invoice->status !== 'Cancelled' && $dueAmount > 0)
                        <div class="row g-4 mb-4">
                            {{-- Counter payment --}}
                            <div class="col-lg-7">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="mb-3"><i class="fa fa-cash-register opacity-50 me-1"></i> Receive Payment
                                    </h5>
                                    <form action="{{ route('invoices.payments.store', $invoice->id) }}" method="POST">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label fs-sm">Amount (&#2547;)</label>
                                                <input type="number" step="0.01" min="0.01"
                                                    max="{{ $dueAmount }}" name="amount"
                                                    class="form-control form-control-sm"
                                                    value="{{ old('amount', $dueAmount) }}">
                                                <x-admin.error-msg name="amount" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fs-sm">Method</label>
                                                <select name="payment_method" class="form-select form-select-sm">
                                                    @foreach (\App\Models\Payment::METHODS as $m)
                                                        <option value="{{ $m }}" @selected(old('payment_method', 'Cash') == $m)>
                                                            {{ $m }}</option>
                                                    @endforeach
                                                </select>
                                                <x-admin.error-msg name="payment_method" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fs-sm">Date</label>
                                                <input type="date" name="payment_date"
                                                    class="form-control form-control-sm"
                                                    value="{{ old('payment_date', now()->format('Y-m-d')) }}">
                                                <x-admin.error-msg name="payment_date" />
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success mt-3">
                                            <i class="fa fa-check opacity-50 me-1"></i> Save Payment
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Online payment --}}
                            <div class="col-lg-5">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="mb-3"><i class="fa fa-credit-card opacity-50 me-1"></i> Pay Online</h5>
                                    <p class="fs-sm text-muted mb-2">Card / bKash / Nagad / Rocket via SSLCommerz
                                        @if (config('sslcommerz.sandbox'))
                                            <span class="badge bg-warning">SANDBOX</span>
                                        @endif
                                    </p>
                                    <form action="{{ route('invoices.pay-online', $invoice->id) }}" method="POST">
                                        @csrf
                                        <label class="form-label fs-sm">Amount (&#2547;)</label>
                                        <input type="number" step="0.01" min="1" max="{{ $dueAmount }}"
                                            name="amount" class="form-control form-control-sm"
                                            value="{{ $dueAmount }}">
                                        <button type="submit" class="btn btn-sm btn-primary mt-3">
                                            <i class="fa fa-external-link-alt opacity-50 me-1"></i> Pay Online
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif ($invoice->status === 'Cancelled')
                        <div class="alert alert-secondary">This invoice is cancelled. Payments cannot be taken.</div>
                    @else
                        <div class="alert alert-success">This invoice is fully paid.</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="fs-sm">Date</th>
                                    <th class="text-center fs-sm">Method</th>
                                    <th class="text-center fs-sm">Amount</th>
                                    <th class="d-none d-md-table-cell text-center fs-sm">Transaction ID</th>
                                    <th class="d-none d-md-table-cell text-center fs-sm">Received By</th>
                                    <th class="text-center fs-sm">Status</th>
                                    @if (auth()->user()->role_id == 1)
                                        <th class="text-center fs-sm" style="width: 100px;">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoice->payments->sortByDesc('id') as $pay)
                                    <tr>
                                        <td>{{ $pay->payment_date?->format('d M Y') }}</td>
                                        <td class="text-center">{{ $pay->payment_method }}</td>
                                        <td class="text-center fw-semibold">&#2547;{{ number_format($pay->amount, 2) }}
                                        </td>
                                        <td class="d-none d-md-table-cell text-center fs-sm">
                                            {{ $pay->transaction_id ?? '—' }}</td>
                                        <td class="d-none d-md-table-cell text-center">{{ $pay->receiver->name ?? '—' }}
                                        </td>
                                        <td class="text-center"><span
                                                class="badge bg-{{ $pay->status_color }}">{{ $pay->status }}</span></td>
                                        @if (auth()->user()->role_id == 1)
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    @unless ($pay->isGateway())
                                                        <a href="{{ route('payments.edit', $pay->id) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit"><i
                                                                class="fa fa-pencil-alt"></i></a>
                                                    @endunless
                                                    <form action="{{ route('payments.destroy', $pay->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Delete this payment? The invoice will be recalculated.{{ $pay->isGateway() && $pay->status === 'Success' ? ' This does NOT refund the customer at the gateway.' : '' }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded"
                                                            title="Delete"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role_id == 1 ? 7 : 6 }}"
                                            class="text-center text-muted">No payments yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Page Content -->
    </main>

    <style>
        @media print {

            #page-header,
            #sidebar,
            #page-footer,
            .no-print {
                display: none !important;
            }

            #page-container,
            #main-container,
            #inv-print-area {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .block {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

@endsection
