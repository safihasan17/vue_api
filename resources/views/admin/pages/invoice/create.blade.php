@extends('admin.layouts.master')

 @section('title', 'Invoices - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="New Invoice" subtitle="Create a billing invoice for a patient">

                 <a href="{{ route('invoices.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to invoices
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
                     <form action="{{ route('invoices.store') }}" method="POST" id="inv-form">
                         @csrf
                         <div class="row">
                             <div class="col-md-12 mb-4">
                                 <label class="form-label" for="inv-patient">Patient</label>
                                 <select class="form-select" name="patient_id" id="inv-patient-select">
                                     <option value="" selected disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                             {{ $patient->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="inv-admission">Related Admission (IPD)
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <select class="form-select" name="admission_id" id="inv-admission-select">
                                     <option value="">None</option>
                                     @foreach ($admissions as $admission)
                                         <option value="{{ $admission->id }}" data-patient="{{ $admission->patient_id }}"
                                             @selected(old('admission_id') == $admission->id)>
                                             #{{ $admission->id }} &mdash; {{ $admission->patient->name ?? 'N/A' }}
                                             (Admitted {{ $admission->admission_date?->format('d M Y') }})</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="admission_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="inv-appointment">Related Appointment (OPD)
                                     <span class="text-muted fs-sm">(optional)</span></label>
                                 <select class="form-select" name="appointment_id" id="inv-appointment-select">
                                     <option value="">None</option>
                                     @foreach ($appointments as $appointment)
                                         <option value="{{ $appointment->id }}" data-patient="{{ $appointment->patient_id }}"
                                             @selected(old('appointment_id') == $appointment->id)>
                                             #{{ $appointment->id }} &mdash; {{ $appointment->patient->name ?? 'N/A' }}
                                             ({{ $appointment->appointment_date?->format('d M Y') }})</option>
                                     @endforeach
                                 </select>
                                 <div class="fs-sm text-muted mt-1">Choose Admission or Appointment, not both.</div>
                                 <x-admin.error-msg name="appointment_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="inv-total">Total Amount (&#2547;)</label>
                                 <input type="number" step="0.01" min="0" class="form-control" name="total_amount"
                                     value="{{ old('total_amount') }}" placeholder="e.g. 5000">
                                 <x-admin.error-msg name="total_amount" />
                             </div>
                             <div class="col-md-8 mb-4">
                                 <label class="form-label">Payment</label>
                                 <div class="form-control-plaintext text-muted">New invoices start as <span class="badge bg-danger">Unpaid</span>.
                                     Receive payments from the invoice page after saving.</div>
                             </div>
                         </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="inv-status">Status</label>
                                 <select class="form-select" name="status">
                                     <option value="Unpaid" @selected(old('status', 'Unpaid') == 'Unpaid')>Unpaid</option>
                                     <option value="Partially Paid" @selected(old('status') == 'Partially Paid')>Partially Paid
                                     </option>
                                     <option value="Paid" @selected(old('status') == 'Paid')>Paid</option>
                                     <option value="Cancelled" @selected(old('status') == 'Cancelled')>Cancelled</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="inv-date">Invoice Date</label>
                                 <input type="date" class="form-control" name="invoice_date"
                                     value="{{ old('invoice_date', now()->format('Y-m-d')) }}">
                                 <x-admin.error-msg name="invoice_date" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Invoice</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection

 @section('script')
     <script>
         // Admission / Appointment are mutually exclusive, and either one
         // (when picked) auto-selects the matching patient.
         (function () {
             const patientSelect = document.getElementById('inv-patient-select');
             const admissionSelect = document.getElementById('inv-admission-select');
             const appointmentSelect = document.getElementById('inv-appointment-select');

             admissionSelect.addEventListener('change', function () {
                 const opt = this.selectedOptions[0];
                 if (this.value) {
                     appointmentSelect.value = '';
                     if (opt.dataset.patient) {
                         patientSelect.value = opt.dataset.patient;
                     }
                 }
             });

             appointmentSelect.addEventListener('change', function () {
                 const opt = this.selectedOptions[0];
                 if (this.value) {
                     admissionSelect.value = '';
                     if (opt.dataset.patient) {
                         patientSelect.value = opt.dataset.patient;
                     }
                 }
             });
         })();
     </script>
 @endsection