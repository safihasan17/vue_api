@extends('admin.layouts.master')

 @section('title', 'Appointments - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Appointment Edit" subtitle="Edit appointment record from this section">

                 <a href="{{ route('appointments.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to appointments
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
                     <form action="{{ route('appointments.update', ['appointment' => $appointment->id]) }}"
                         method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ap-patient">Patient</label>
                                 <select class="form-select" name="patient_id" id="ap-patient">
                                     <option value="" disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}"
                                             @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                             {{ $patient->name }}
                                         </option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="ap-doctor">Doctor</label>
                                 <select class="form-select" name="doctor_id" id="ap-doctor">
                                     <option value="" disabled>Select Doctor</option>
                                     @foreach ($doctors as $doctor)
                                         <option value="{{ $doctor->id }}"
                                             @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                                             {{ $doctor->user->name ?? 'Doctor #' . $doctor->id }}{{ $doctor->department ? ' — ' . $doctor->department->name : '' }}
                                         </option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="doctor_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ap-date">Date</label>
                                 <input type="date" class="form-control" name="appointment_date"
                                     value="{{ old('appointment_date', optional($appointment->appointment_date)->format('Y-m-d')) }}">
                                 <x-admin.error-msg name="appointment_date" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ap-time">Time</label>
                                 <input type="time" class="form-control" name="appointment_time"
                                     value="{{ old('appointment_time', $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') : '') }}">
                                 <x-admin.error-msg name="appointment_time" />
                             </div>
                             <div class="col-md-4 mb-4">
                                 <label class="form-label" for="ap-status">Status</label>
                                 <select class="form-select" name="status" id="ap-status">
                                     <option value="Scheduled" @selected(old('status', $appointment->status) == 'Scheduled')>Scheduled</option>
                                     <option value="Checked-in" @selected(old('status', $appointment->status) == 'Checked-in')>Checked-in</option>
                                     <option value="Waiting" @selected(old('status', $appointment->status) == 'Waiting')>Waiting</option>
                                     <option value="Completed" @selected(old('status', $appointment->status) == 'Completed')>Completed</option>
                                     <option value="Cancelled" @selected(old('status', $appointment->status) == 'Cancelled')>Cancelled</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-12 mb-4">
                                 <label class="form-label" for="ap-reason">Reason for Visit</label>
                                 <textarea class="form-control" name="reason" id="ap-reason" rows="2">{{ old('reason', $appointment->reason) }}</textarea>
                                 <x-admin.error-msg name="reason" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Appointment</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection