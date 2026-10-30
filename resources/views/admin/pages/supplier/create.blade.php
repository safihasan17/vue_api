@extends('admin.layouts.master')

@section('title', 'Suppliers - Create')

@section('content')
    <div class="block block-rounded block-transparent mt-5">
        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Add Supplier" subtitle="Add a new medicine supplier">
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to suppliers
                </a>
            </x-admin.phead>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        
                        @include('admin.pages.supplier._form')
                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
