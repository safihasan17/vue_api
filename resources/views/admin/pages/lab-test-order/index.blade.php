@extends('admin.layouts.master')

@section('title', 'Lab Test Orders')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Lab Test Orders" subtitle="Track ordered lab tests and their results">

                <a href="{{ route('lab-test-orders.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> New Order
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row items-push">
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-warning">{{ $pendingCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Pending</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-info">{{ $inProgressCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">In Progress</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $completedCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Completed</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-danger">{{ $cancelledCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Cancelled</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">All Orders</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Patient</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Test</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Doctor</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Order Date</th>
                                            <th class="text-center fs-sm">Status</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($labTestOrders as $item)
                                            <tr>

                                                <td>
                                                    <a class="fw-semibold"
                                                        href="{{ route('lab-test-orders.show', $item->id) }}">{{ $item->patient->name ?? 'N/A' }}</a>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    {{ $item->test->test_name ?? 'N/A' }}</td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    {{ $item->doctor->user->name ?? 'N/A' }}</td>
                                                <td class="d-none d-md-table-cell text-center">
                                                    {{ $item->order_date?->format('d M Y') }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $statusColors = [
                                                            'Pending' => 'warning',
                                                            'In Progress' => 'info',
                                                            'Completed' => 'success',
                                                            'Cancelled' => 'danger',
                                                        ];
                                                        $statusColor = $statusColors[$item->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('lab-test-orders.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('lab-test-orders.edit', ['lab_test_order' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}"
                                                            data-name="{{ $item->patient->name ?? '' }}" title="Delete row"
                                                            data-bs-toggle="modal" data-bs-target="#modalDelete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>

                                                        <form
                                                            action="{{ route('lab-test-orders.generate-invoice', $item->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-success rounded"
                                                                title="Generate invoice (this patient's all tests of this date)">
                                                                <i class="fa fa-file-invoice"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>

                            <div class="table-footer-control">
                                {{ $labTestOrders->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- END Page Content -->
    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Lab Test Order">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to Delete this order</p>
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
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.dataset.id;
                let name = this.dataset.name;

                document.querySelector('#modalDelete .name').innerText = name;
                document.querySelector('#modalDelete form').action =
                    `{{ route('lab-test-orders.destroy', ['lab_test_order' => ':id']) }}`.replace(':id',
                    id);

            })
        })
    </script>

@endsection
