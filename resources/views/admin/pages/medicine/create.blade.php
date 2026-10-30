@extends('admin.layouts.master')

@section('title', 'Medicines - Create')

@section('content')
    <div class="block block-rounded block-transparent mt-5">
        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Add Medicine" subtitle="Add a new medicine to the pharmacy">
                <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to medicines
                </a>
            </x-admin.phead>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('medicines.store') }}" method="POST">
                        @csrf
                        @include('admin.pages.medicine._form')
                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
