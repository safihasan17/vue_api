@extends('admin.layouts.master')

@section('title', 'Invoice Items')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Invoice Items" subtitle="Line items that make up each invoice">

                <a href="{{ route('invoice-items.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Item
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
                            <h3 class="block-title">All Invoice Items</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Invoice</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Type</th>
                                            <th class="fs-sm">Description</th>
                                            <th class="text-center fs-sm">Amount</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($invoiceItems as $item)
                                            <tr>

                                                <td>
                                                    <a class="fw-semibold"
                                                        href="{{ route('invoices.show', $item->invoice_id) }}">INV-{{ str_pad($item->invoice_id, 4, '0', STR_PAD_LEFT) }}</a>
                                                    <div class="fs-sm text-muted">{{ $item->invoice->patient->name ?? 'N/A' }}</div>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    <span class="badge bg-secondary">{{ $item->item_type }}</span>
                                                </td>
                                                <td>{{ $item->description }}</td>
                                                <td class="text-center">৳{{ number_format($item->amount, 2) }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('invoice-items.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('invoice-items.edit', ['invoice_item' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->description }}"
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
                                {{ $invoiceItems->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- END Page Content -->
    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Invoice Item">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to Delete this item</p>
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
        document.querySelector('#modalDelete form').action = `{{ route('invoice-items.destroy' , ['invoice_item'=>':id']) }}` .replace(':id', id);

    })
})
</script>

@endsection