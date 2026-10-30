@extends('admin.layouts.master')

@section('title', 'Invoices')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Billing / Invoices" subtitle="Manage patient invoices and payments">

                <a href="{{ route('invoices.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> New Invoice
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
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalInvoices }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Invoices</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-danger">{{ $unpaidCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Unpaid</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $paidCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Paid</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-warning">৳{{ number_format($totalDue, 2) }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Due</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">All Invoices</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Invoice</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Patient</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Type</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Total</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Paid</th>
                                            <th class="text-center fs-sm">Status</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($invoices as $item)
                                            <tr>

                                                <td>
                                                    <a class="fw-semibold"
                                                        href="{{ route('invoices.show', $item->id) }}">INV-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</a>
                                                    <div class="fs-sm text-muted">{{ $item->invoice_date?->format('d M Y') }}</div>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->patient->name ?? 'N/A' }}</td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    @if ($item->admission_id)
                                                        <span class="badge bg-info">IPD</span>
                                                    @elseif ($item->appointment_id)
                                                        <span class="badge bg-secondary">OPD</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="d-none d-md-table-cell text-center">৳{{ number_format($item->total_amount, 2) }}</td>
                                                <td class="d-none d-md-table-cell text-center">৳{{ number_format($item->paid_amount, 2) }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $statusColors = [
                                                            'Unpaid' => 'danger',
                                                            'Partially Paid' => 'warning',
                                                            'Paid' => 'success',
                                                            'Cancelled' => 'secondary',
                                                        ];
                                                        $statusColor = $statusColors[$item->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('invoices.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('invoices.edit', ['invoice' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}"
                                                            data-name="INV-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}"
                                                            title="Delete row" data-bs-toggle="modal"
                                                            data-bs-target="#modalDelete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>

                            <div class="table-footer-control">
                                {{ $invoices->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- END Page Content -->
    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Invoice">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to Delete this invoice</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>

            <hr>

            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" title="Delete row"
                    data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn  btn-danger" title="Delete row"
                    data-bs-dismiss="modal">Delete</button>
            </form>

        </div>
    </x-admin.modal>
@endsection


@section('script')

<script>
document.querySelectorAll('.delete').forEach(button=>{
    button.addEventListener('click', function(){
        let id = this.dataset.id;
        let name = this.dataset.name;

        document.querySelector('#modalDelete .name').innerText = name;
        document.querySelector('#modalDelete form').action = `{{ route('invoices.destroy' , ['invoice'=>':id']) }}` .replace(':id', id);

    })
})
</script>

@endsection