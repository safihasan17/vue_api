@extends('admin.layouts.master')

@section('title', 'Admissions')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Wards, Beds & Admissions" subtitle="Track patient admissions, wards and beds">

                <a href="{{ route('admissions.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> New Admission
                </a>

            </x-admin.phead>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            <div class="row items-push">
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-primary">{{ $totalAdmissions }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Admissions</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-danger">{{ $currentlyAdmitted }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Currently Admitted</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $dischargedCount }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Discharged</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">All Admissions</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Patient</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Doctor</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Ward / Bed</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Admission Date</th>
                                            <th class="text-center fs-sm">Status</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($admissions as $item)
                                            <tr>

                                                <td>
                                                    <a class="fw-semibold"
                                                        href="{{ route('admissions.show', $item->id) }}">{{ $item->patient->name ?? 'N/A' }}</a>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">{{ $item->doctor->user->name ?? 'N/A' }}</td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    {{ $item->ward->name ?? '-' }} / {{ $item->bed->bed_number ?? '-' }}
                                                </td>
                                                <td class="d-none d-md-table-cell text-center">{{ $item->admission_date?->format('d M Y') }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $statusColors = [
                                                            'Admitted' => 'danger',
                                                            'Discharged' => 'success',
                                                            'Transferred' => 'info',
                                                        ];
                                                        $statusColor = $statusColors[$item->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('admissions.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('admissions.edit', ['admission' => $item->id]) }}"
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

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>

                            <div class="table-footer-control">
                                {{ $admissions->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- END Page Content -->
    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Admission">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to Delete this admission record</p>
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
        document.querySelector('#modalDelete form').action = `{{ route('admissions.destroy' , ['admission'=>':id']) }}` .replace(':id', id);

    })
})
</script>

@endsection
