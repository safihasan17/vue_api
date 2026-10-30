 @extends('admin.layouts.master')

 @section('title', 'Doctors - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Doctor Create" subtitle="Add a new doctor profile from this section">

                 <a href="{{ route('doctors.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to doctors
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
                     <form action="{{ route('doctors.store') }}" method="POST"  enctype='multipart/form-data'>
                         @csrf
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-user">Doctors</label>
                                 <select class="form-select" name="user_id">
                                     <option value="" selected disabled>Select  Doctor</option>
                                     @foreach ($users as $user)
                                         <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                             {{ $user->name }} ({{ $user->email }})</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="user_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-dept">Department</label>
                                 <select class="form-select" name="department_id">
                                     <option value="" selected disabled>Select department</option>
                                     @foreach ($departments as $department)
                                         <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                             {{ $department->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="department_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-spec">Specialization</label>
                                 <input type="text" class="form-control" name="specialization"
                                     value="{{ old('specialization') }}" placeholder="e.g. Cardiology">
                                 <x-admin.error-msg name="specialization" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-fee">Consultation Fee (&#2547;)</label>
                                 <input type="number" step="0.01" min="0" class="form-control" name="consultation_fee"
                                     value="{{ old('consultation_fee') }}" placeholder="e.g. 1200">
                                 <x-admin.error-msg name="consultation_fee" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-qual">Qualification</label>
                                 <input type="text" class="form-control" name="qualification"
                                     value="{{ old('qualification') }}" placeholder="e.g. MBBS, MD (Cardiology)">
                                 <x-admin.error-msg name="qualification" />
                             </div>
                              <div class="col-md-6 mb-4">
                                 <label class="form-label" for="dr-qual">Image</label>
                                 <input type="file" class="form-control" name="image"
                                     value="{{ old('image') }}" placeholder="">
                                 <x-admin.error-msg name="image" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('doctors.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Doctor</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection
