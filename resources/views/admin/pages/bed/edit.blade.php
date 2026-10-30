 @extends('admin.layouts.master')

 @section('title', 'Beds - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Edit Bed" subtitle="Update bed details from this section">

                 <a href="{{ route('beds.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to beds
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
                     <form action="{{ route('beds.update', ['bed' => $bed->id]) }}" method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="bed-ward">Ward</label>
                                 <select class="form-select" name="ward_id">
                                     <option value="" disabled>Select Ward</option>
                                     @foreach ($wards as $ward)
                                         <option value="{{ $ward->id }}" @selected(old('ward_id', $bed->ward_id) == $ward->id)>
                                             {{ $ward->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="ward_id" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="bed-number">Bed Number</label>
                                 <input type="text" class="form-control" name="bed_number"
                                     value="{{ old('bed_number', $bed->bed_number) }}" placeholder="e.g. B-12">
                                 <x-admin.error-msg name="bed_number" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="bed-status">Status</label>
                                 <select class="form-select" name="status">
                                     <option value="Available" @selected(old('status', $bed->status) == 'Available')>Available</option>
                                     <option value="Occupied" @selected(old('status', $bed->status) == 'Occupied')>Occupied</option>
                                     <option value="Reserved" @selected(old('status', $bed->status) == 'Reserved')>Reserved</option>
                                     <option value="Maintenance" @selected(old('status', $bed->status) == 'Maintenance')>
                                         Maintenance</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('beds.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Bed</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection
