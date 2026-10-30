 @extends('admin.layouts.master')

 @section('title', 'Patients - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Patient Edit" subtitle="Edit patient record from this section">

                 <a href="{{ route('patients.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to patients
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
                     <form action="{{ route('patients.update', ['patient' => $patient->id]) }}" method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="pt-name">Full Name</label>
                                 <input type="text" class="form-control" name="name"
                                     value="{{ old('name', $patient->name) }}" placeholder="e.g. Rafiq Ahmed">
                                 <x-admin.error-msg name="name" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="pt-dob">Date of Birth</label>
                                 <input type="date" class="form-control" name="dob"
                                     value="{{ old('dob', optional($patient->dob)->format('Y-m-d')) }}">
                                 <x-admin.error-msg name="dob" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="pt-gender">Gender</label>
                                 <select class="form-select" name="gender">
                                     <option value="" disabled>Select Gender</option>
                                     <option value="Male" @selected(old('gender', $patient->gender) == 'Male')>Male</option>
                                     <option value="Female" @selected(old('gender', $patient->gender) == 'Female')>Female</option>
                                     <option value="Other" @selected(old('gender', $patient->gender) == 'Other')>Other</option>
                                 </select>
                                 <x-admin.error-msg name="gender" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="pt-blood">Blood Group</label>
                                 <select class="form-select" name="blood_group">
                                     <option value="" disabled>Select</option>
                                     @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                         <option value="{{ $bg }}" @selected(old('blood_group', $patient->blood_group) == $bg)>
                                             {{ $bg }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="blood_group" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="pt-phone">Phone</label>
                                 <input type="text" class="form-control" name="phone"
                                     value="{{ old('phone', $patient->phone) }}" placeholder="017XX-XXXXXX">
                                 <x-admin.error-msg name="phone" />
                             </div>
                             <div class="col-md-5 mb-4">
                                 <label class="form-label" for="pt-emergency">Emergency Contact</label>
                                 <input type="text" class="form-control" name="emergency_contact"
                                     value="{{ old('emergency_contact', $patient->emergency_contact) }}"
                                     placeholder="01XXX-XXXXXX">
                                 <x-admin.error-msg name="emergency_contact" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="pt-guardian">Guardian Name</label>
                                 <input type="text" class="form-control" name="guardian_name"
                                     value="{{ old('guardian_name', $patient->guardian_name) }}" placeholder="N/A">
                                 <x-admin.error-msg name="guardian_name" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="pt-address">Address</label>
                                 <textarea class="form-control" name="address" rows="2">{{ old('address', $patient->address) }}</textarea>
                                 <x-admin.error-msg name="address" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('patients.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Patient</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection
