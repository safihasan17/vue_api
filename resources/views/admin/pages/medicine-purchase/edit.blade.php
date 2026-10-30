@extends('admin.layouts.master')

@section('title', 'Purchases - Edit')

@section('content')
    <div class="block block-rounded block-transparent mt-5">
        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="Edit Purchase" subtitle="Changing quantity or medicine adjusts stock automatically">
                <a href="{{ route('medicine-purchases.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to purchases
                </a>
            </x-admin.phead>

            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('medicine-purchases.update', $purchase->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.pages.medicine-purchase._form')
                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('medicine-purchases.index') }}" class="btn btn-sm btn-alt-secondary me-1">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Update Purchase</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
