@extends('admin.layouts.master')

@section('title', 'wards - Edit')

@section('content')

    <div class="block block-rounded block-transparent mt-5">

        <div class="block-content fs-sm mt-2">
            <x-admin.phead title="wards edit" subtitle="Edit ward from this section">

                <a href="{{ route('wards.index') }}" type="button" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i> back to wards
                </a>

            </x-admin.phead>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif


            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('wards.update', ['ward' => $ward->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="ward-name">Ward Name</label>
                                <input type="text" class="form-control" id="ward-name" name="name"
                                    placeholder="e.g. Ward A — General" value="{{ old('name', $ward->name) }}">
                                <x-admin.error-msg name="name" />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="ward-floor">Floor</label>
                                <select class="form-select" id="ward-floor" name="floor">
                                    <option value="" disabled >Select a Floor</option>
                                    @foreach ($floors as $item)
                                        <option value="{{ $item }}" @selected(old('floor', $ward->floor) == $item)>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                <x-admin.error-msg name="floor" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="ward-type">Type</label>
                                <select class="form-select" id="ward-type" name="type">
                                    <option value="" disabled >Select a Type</option>
                                    @foreach ($types as $item)
                                        <option value="{{ $item }}" @selected(old('type', $ward->type) == $item)>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                <x-admin.error-msg name="type" />
                            </div>
                        </div>

                        <div class="block-content block-content-full text-end bg-body">
                            <a href="{{ route('wards.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary">Update
                                Ward</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

@endsection