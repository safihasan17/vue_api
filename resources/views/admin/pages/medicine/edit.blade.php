@extends('admin.layouts.master')

@section('title', 'Medicines - Edit')

@section('content')
    <div class="block block-rounded block-transparent mt-5">
        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Edit Medicine" subtitle="Update medicine details from this section">
                <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to medicines
                </a>
            </x-admin.phead>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('medicines.update', $medicine->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.pages.medicine._form')
                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Update Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
