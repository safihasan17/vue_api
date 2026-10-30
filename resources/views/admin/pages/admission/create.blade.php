@extends('admin.layouts.master')

 @section('title', 'Admissions - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="New Admission" subtitle="Admit a patient to a ward and bed">

                 <a href="{{ route('admissions.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to admissions
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
                     <form action="{{ route('admissions.store') }}" method="POST">
                         @csrf

                         @isset($prescription)
                             <input type="hidden" name="prescription_id" value="{{ $prescription->id }}">
                             <div class="alert alert-info">
                                 Admission suggested by
                                 <strong>Dr. {{ $prescription->doctor->user->name ?? 'N/A' }}</strong>
                                 in prescription
                                 <strong>RX-{{ str_pad($prescription->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                 for <strong>{{ $prescription->patient->name ?? 'N/A' }}</strong>.
                                 Choose the ward &amp; bed below.
                             </div>
                         @endisset

                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-patient">Patient</label>
                                 {{-- locked when coming from a prescription (hidden input sends the value) --}}
                                 @isset($prescription)
                                     <input type="hidden" name="patient_id" value="{{ $prescription->patient_id }}">
                                 @endisset
                                 <select class="form-select" name="patient_id" @disabled(isset($prescription))>
                                     <option value="" selected disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}" @selected(old('patient_id', $prescription->patient_id ?? null) == $patient->id)>
                                             {{ $patient->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-doctor">Doctor</label>
                                 <select class="form-select" name="doctor_id">
                                     <option value="" selected disabled>Select Doctor</option>
                                     @foreach ($doctors as $doctor)
                                         <option value="{{ $doctor->id }}" @selected(old('doctor_id', $prescription->doctor_id ?? null) == $doctor->id)>
                                             {{ $doctor->user->name ?? 'N/A' }} &mdash;
                                             {{ $doctor->specialization }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="doctor_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ad-ward">Ward</label>
                                 <select class="form-select" name="ward_id" id="ad-ward-select">
                                     <option value="" selected disabled>Select Ward</option>
                                     @foreach ($wards as $ward)
                                         <option value="{{ $ward->id }}" @selected(old('ward_id') == $ward->id)>
                                             {{ $ward->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="ward_id" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ad-bed">Bed <span class="text-muted fs-sm">(available only)</span></label>
                                 <select class="form-select" name="bed_id" id="ad-bed-select">
                                     <option value="" selected disabled>Select Ward first</option>
                                     @foreach ($beds as $bed)
                                         <option value="{{ $bed->id }}" data-ward="{{ $bed->ward_id }}"
                                             @selected(old('bed_id') == $bed->id)>
                                             {{ $bed->bed_number }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="bed_id" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ad-status">Status</label>
                                 <select class="form-select" name="status">
                                     <option value="Admitted" @selected(old('status', 'Admitted') == 'Admitted')>Admitted</option>
                                     <option value="Discharged" @selected(old('status') == 'Discharged')>Discharged</option>
                                     <option value="Transferred" @selected(old('status') == 'Transferred')>Transferred</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-admission-date">Admission Date &amp; Time</label>
                                 <input type="datetime-local" class="form-control" name="admission_date"
                                     value="{{ old('admission_date', now()->format('Y-m-d\TH:i')) }}">
                                 <x-admin.error-msg name="admission_date" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-discharge-date">Discharge Date &amp; Time
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <input type="datetime-local" class="form-control" name="discharge_date"
                                     value="{{ old('discharge_date') }}">
                                 <x-admin.error-msg name="discharge_date" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Admission</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection

 @section('script')
     <script>
         (function () {
             const wardSelect = document.getElementById('ad-ward-select');
             const bedSelect = document.getElementById('ad-bed-select');
             const allBedOptions = Array.from(bedSelect.options).filter(opt => opt.value !== '');

             function filterBeds() {
                 const wardId = wardSelect.value;
                 const currentValue = bedSelect.value;

                 bedSelect.innerHTML = '';

                 const placeholder = document.createElement('option');
                 placeholder.value = '';
                 placeholder.disabled = true;
                 placeholder.selected = true;
                 placeholder.textContent = wardId ? 'Select Bed' : 'Select Ward first';
                 bedSelect.appendChild(placeholder);

                 allBedOptions
                     .filter(opt => opt.dataset.ward === wardId)
                     .forEach(opt => {
                         const clone = opt.cloneNode(true);
                         if (clone.value === currentValue) {
                             clone.selected = true;
                             placeholder.selected = false;
                         }
                         bedSelect.appendChild(clone);
                     });
             }

             wardSelect.addEventListener('change', filterBeds);

             // Run once on load in case of validation error repopulate (old ward_id set)
             if (wardSelect.value) {
                 filterBeds();
             }
         })();
     </script>
 @endsection