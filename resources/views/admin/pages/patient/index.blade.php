@extends('admin.layouts.master')

@section('title', 'patients')

@section('content')
    <main id="main-container">

        <div class="content">
            <x-admin.phead title="Patients" subtitle="Manage patient records and medical histories">

                <a href="{{ route('patients.create') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> Add Patient
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
                            <div class="fs-2 fw-semibold text-primary">{{ $totalPatients }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">Total Patients</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-info">{{ $newThisMonth }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">New This Month</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                        <div class="block-content block-content-full">
                            <div class="fs-2 fw-semibold text-success">{{ $totalGuardians }}</div>
                        </div>
                        <div class="block-content py-2 bg-body-light">
                            <p class="fw-medium fs-sm text-muted mb-0">With Guardian Info</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">All Patients</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="fs-sm">Patient</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Age / Gender</th>
                                            <th class="d-none d-sm-table-cell text-center fs-sm">Blood Group</th>
                                            <th class="d-none d-md-table-cell text-center fs-sm">Phone</th>
                                            <th class="d-none d-xl-table-cell text-center fs-sm">Guardian</th>
                                            <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($patients as $item)
                                            <tr>

                                                <td>
                                                    <a class="fw-semibold"
                                                        href="{{ route('patients.show', $item->id) }}">{{ $item->name }}</a>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    {{ $item->gender }}{{ $item->dob ? ', ' . $item->dob->age : '' }}
                                                </td>
                                                <td class="d-none d-sm-table-cell text-center">
                                                    {{ $item->blood_group ?? '-' }}</td>
                                                <td class="d-none d-md-table-cell text-center">{{ $item->phone }}</td>
                                                <td class="d-none d-xl-table-cell text-center fs-sm">
                                                    {{ $item->guardian_name ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        <a href="{{ route('patients.show', $item->id) }}"
                                                            class="btn btn-sm btn-outline-primary rounded" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('patients.edit', ['patient' => $item->id]) }}"
                                                            class="btn btn-sm btn-outline-warning rounded" title="Edit">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </a>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning rounded table-btn-action delete"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
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
                                {{ $patients->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- END Page Content -->


    </main>

    {{-- modal --}}

    <x-admin.modal id="modalDelete" title="Delete Patient">
        <div class="text-center">
            <i class="bi bi-trash fs-1 text-danger"></i> <br>
            <p> Are you sure you want to Delete this Patient</p>
            <span class="name fw-bold badge border border-danger text-danger py-2 px-3"></span>

            <hr>

            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-secondary" title="Delete row"
                    data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn  btn-danger" title="Delete row" data-bs-dismiss="modal">Delete</button>
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
                    `{{ route('patients.destroy', ['patient' => ':id']) }}`.replace(':id', id);

            })
        })
    </script>

@endsection
