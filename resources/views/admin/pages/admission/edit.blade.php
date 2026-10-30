 @extends('admin.layouts.master')

 @section('title', 'Admissions - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Edit Admission" subtitle="Update admission details from this section">

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
                     <form action="{{ route('admissions.update', ['admission' => $admission->id]) }}" method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-patient">Patient</label>
                                 <select class="form-select" name="patient_id">
                                     <option value="" disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}"
                                             @selected(old('patient_id', $admission->patient_id) == $patient->id)>
                                             {{ $patient->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-doctor">Doctor</label>
                                 <select class="form-select" name="doctor_id">
                                     <option value="" disabled>Select Doctor</option>
                                     @foreach ($doctors as $doctor)
                                         <option value="{{ $doctor->id }}"
                                             @selected(old('doctor_id', $admission->doctor_id) == $doctor->id)>
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
                                     <option value="" disabled>Select Ward</option>
                                     @foreach ($wards as $ward)
                                         <option value="{{ $ward->id }}"
                                             @selected(old('ward_id', $admission->ward_id) == $ward->id)>
                                             {{ $ward->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="ward_id" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ad-bed">Bed</label>
                                 <select class="form-select" name="bed_id" id="ad-bed-select">
                                     <option value="" disabled>Select Bed</option>
                                     @foreach ($beds as $bed)
                                         <option value="{{ $bed->id }}" data-ward="{{ $bed->ward_id }}"
                                             @selected(old('bed_id', $admission->bed_id) == $bed->id)>
                                             {{ $bed->bed_number }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="bed_id" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ad-status">Status</label>
                                 <select class="form-select" name="status">
                                     <option value="Admitted" @selected(old('status', $admission->status) == 'Admitted')>Admitted
                                     </option>
                                     <option value="Discharged" @selected(old('status', $admission->status) == 'Discharged')>
                                         Discharged</option>
                                     <option value="Transferred" @selected(old('status', $admission->status) == 'Transferred')>
                                         Transferred</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-admission-date">Admission Date &amp; Time</label>
                                 <input type="datetime-local" class="form-control" name="admission_date"
                                     value="{{ old('admission_date', $admission->admission_date?->format('Y-m-d\TH:i')) }}">
                                 <x-admin.error-msg name="admission_date" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ad-discharge-date">Discharge Date &amp; Time
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <input type="datetime-local" class="form-control" name="discharge_date"
                                     value="{{ old('discharge_date', $admission->discharge_date?->format('Y-m-d\TH:i')) }}">
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
                 placeholder.textContent = 'Select Bed';
                 bedSelect.appendChild(placeholder);

                 let matched = false;
                 allBedOptions
                     .filter(opt => opt.dataset.ward === wardId)
                     .forEach(opt => {
                         const clone = opt.cloneNode(true);
                         if (clone.value === currentValue) {
                             clone.selected = true;
                             matched = true;
                         }
                         bedSelect.appendChild(clone);
                     });

                 placeholder.selected = !matched;
             }

             wardSelect.addEventListener('change', filterBeds);

             // Initial load: filter beds down to the currently selected ward
             filterBeds();
         })();
     </script>
 @endsection
