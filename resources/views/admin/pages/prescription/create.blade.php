@extends('admin.layouts.master')

 @section('title', 'Prescriptions - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="New Prescription" subtitle="Issue a new prescription for a patient">

                 <a href="{{ route('prescriptions.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to prescriptions
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
                     <form action="{{ route('prescriptions.store') }}" method="POST" id="rx-form">
                         @csrf
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="rx-patient">Patient</label>
                                 <select class="form-select" name="patient_id">
                                     <option value="" selected disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                             {{ $patient->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="rx-doctor">Doctor</label>
                                 <select class="form-select" name="doctor_id">
                                     <option value="" selected disabled>Select Doctor</option>
                                     @foreach ($doctors as $doctor)
                                         <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                                             {{ $doctor->user->name ?? 'N/A' }} &mdash;
                                             {{ $doctor->specialization }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="doctor_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="rx-appointment">Related Appointment
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <select class="form-select" name="appointment_id">
                                     <option value="">None</option>
                                     @foreach ($appointments as $appointment)
                                         <option value="{{ $appointment->id }}"
                                             @selected(old('appointment_id', request()->query('appointment_id')) == $appointment->id)>
                                             #{{ $appointment->id }} &mdash; {{ $appointment->patient->name ?? 'N/A' }}
                                             ({{ $appointment->appointment_date?->format('d M Y') }})</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="appointment_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="rx-date">Prescription Date</label>
                                 <input type="date" class="form-control" name="prescription_date"
                                     value="{{ old('prescription_date', now()->format('Y-m-d')) }}">
                                 <x-admin.error-msg name="prescription_date" />
                             </div>
                         </div>

                         <div class="mb-4">
                             <p class="fw-semibold mb-2">Medicines</p>

                             <div id="medicine-rows">
                                 {{-- rows injected here by JS --}}
                             </div>

                             <button type="button" class="btn btn-sm btn-alt-secondary" id="add-medicine-row">
                                 <i class="fa fa-plus me-1"></i> Add Medicine
                             </button>
                             <x-admin.error-msg name="medicines" />
                         </div>

                         <div class="mb-4">
                             <p class="fw-semibold mb-2">Lab Tests</p>

                             <div id="labtest-rows">
                                 {{-- rows injected here by JS --}}
                             </div>

                             <button type="button" class="btn btn-sm btn-alt-secondary" id="add-labtest-row">
                                 <i class="fa fa-plus me-1"></i> Add Lab Test
                             </button>
                             <x-admin.error-msg name="lab_tests" />
                         </div>

                         <div class="row">
                             <div class="col-md-12 mb-4">
                                 <label class="form-label" for="rx-notes">General Notes / Instructions
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                                 <x-admin.error-msg name="notes" />
                             </div>
                         </div>

                         @include('admin.pages.prescription._admission-fields')

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('prescriptions.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Prescription</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

     {{-- Hidden template row (cloned by JS) --}}
     <template id="medicine-row-template">
         <div class="row g-2 mb-2 medicine-row align-items-start">
             <div class="col-md-4">
                 <select class="form-select" name="medicines[__INDEX__][medicine_id]">
                     <option value="" selected disabled>Select medicine</option>
                     @foreach ($medicines as $medicine)
                         <option value="{{ $medicine->id }}">{{ $medicine->name }}
                             @if ($medicine->generic_name)
                                 ({{ $medicine->generic_name }})
                             @endif
                         </option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-3">
                 <input type="text" class="form-control" name="medicines[__INDEX__][dosage]"
                     placeholder="Dosage e.g. 500mg 2x/day">
             </div>
             <div class="col-md-2">
                 <input type="text" class="form-control" name="medicines[__INDEX__][duration]"
                     placeholder="e.g. 7 days">
             </div>
             <div class="col-md-2">
                 <input type="text" class="form-control" name="medicines[__INDEX__][instructions]"
                     placeholder="Instructions">
             </div>
             <div class="col-md-1">
                 <button type="button" class="btn btn-alt-secondary w-100 remove-medicine-row" title="Remove">
                     <i class="fa fa-times"></i>
                 </button>
             </div>
         </div>
     </template>

     {{-- Hidden template row for Lab Tests --}}
     <template id="labtest-row-template">
         <div class="row g-2 mb-2 labtest-row align-items-start">
             <div class="col-md-10">
                 <select class="form-select" name="lab_tests[__INDEX__][test_id]">
                     <option value="" selected disabled>Select lab test</option>
                     @foreach ($labTests as $test)
                         <option value="{{ $test->id }}">{{ $test->test_name }}
                             @if ($test->category)
                                 ({{ $test->category }})
                             @endif
                         </option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-2">
                 <button type="button" class="btn btn-alt-secondary w-100 remove-labtest-row" title="Remove">
                     <i class="fa fa-times"></i>
                 </button>
             </div>
         </div>
     </template>

 @endsection

 @section('script')
     <script>
         (function () {
             const container = document.getElementById('medicine-rows');
             const template = document.getElementById('medicine-row-template');
             let rowIndex = 0;

             function addRow() {
                 const html = template.innerHTML.replaceAll('__INDEX__', rowIndex);
                 const wrapper = document.createElement('div');
                 wrapper.innerHTML = html.trim();
                 container.appendChild(wrapper.firstElementChild);
                 rowIndex++;
             }

             document.getElementById('add-medicine-row').addEventListener('click', addRow);

             container.addEventListener('click', function (e) {
                 const btn = e.target.closest('.remove-medicine-row');
                 if (btn) {
                     btn.closest('.medicine-row').remove();
                 }
             });

             // Start with one empty row
             addRow();
         })();

         // Lab Tests dynamic rows
         (function () {
             const container = document.getElementById('labtest-rows');
             const template = document.getElementById('labtest-row-template');
             let rowIndex = 0;

             function addRow() {
                 const html = template.innerHTML.replaceAll('__INDEX__', rowIndex);
                 const wrapper = document.createElement('div');
                 wrapper.innerHTML = html.trim();
                 container.appendChild(wrapper.firstElementChild);
                 rowIndex++;
             }

             document.getElementById('add-labtest-row').addEventListener('click', addRow);

             container.addEventListener('click', function (e) {
                 const btn = e.target.closest('.remove-labtest-row');
                 if (btn) {
                     btn.closest('.labtest-row').remove();
                 }
             });

            
         })();

         // Auto-select AND lock patient & doctor based on the chosen appointment
         (function () {
             const appointmentMap = @json($appointmentMap);

             const appointmentSelect = document.querySelector('select[name="appointment_id"]');
             const patientSelect = document.querySelector('select[name="patient_id"]');
             const doctorSelect = document.querySelector('select[name="doctor_id"]');
             const form = document.getElementById('rx-form');

             function lock(select) {
                 select.setAttribute('disabled', 'disabled');
                 select.classList.add('bg-body-light');
             }

             function unlock(select) {
                 select.removeAttribute('disabled');
                 select.classList.remove('bg-body-light');
             }

             function applySelection() {
                 const data = appointmentMap[appointmentSelect.value];

                 if (data) {
                     patientSelect.value = data.patient_id;
                     doctorSelect.value = data.doctor_id;
                     lock(patientSelect);
                     lock(doctorSelect);
                 } else {
                     
                     unlock(patientSelect);
                     unlock(doctorSelect);
                 }
             }

             appointmentSelect.addEventListener('change', applySelection);

             
             if (appointmentSelect.value) {
                 applySelection();
             }

             
             form.addEventListener('submit', function () {
                 unlock(patientSelect);
                 unlock(doctorSelect);
             });
         })();
     </script>
 @endsection