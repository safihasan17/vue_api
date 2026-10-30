 @extends('admin.layouts.master')

 @section('title', 'Lab Test Orders - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="Edit Lab Test Order" subtitle="Update order status and result">

                 <a href="{{ route('lab-test-orders.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to orders
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
                     <form action="{{ route('lab-test-orders.update', ['lab_test_order' => $labTestOrder->id]) }}"
                         method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="lto-patient">Patient</label>
                                 <select class="form-select" name="patient_id">
                                     <option value="" disabled>Select Patient</option>
                                     @foreach ($patients as $patient)
                                         <option value="{{ $patient->id }}"
                                             @selected(old('patient_id', $labTestOrder->patient_id) == $patient->id)>
                                             {{ $patient->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="patient_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="lto-doctor">Doctor</label>
                                 <select class="form-select" name="doctor_id">
                                     <option value="" disabled>Select Doctor</option>
                                     @foreach ($doctors as $doctor)
                                         <option value="{{ $doctor->id }}"
                                             @selected(old('doctor_id', $labTestOrder->doctor_id) == $doctor->id)>
                                             {{ $doctor->user->name ?? 'N/A' }} &mdash;
                                             {{ $doctor->specialization }}</option>
                                     @endforeach
                                 </select>
                                 <x-admin.error-msg name="doctor_id" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="lto-test">Lab Test</label>
                                 <select class="form-select" name="test_id" id="lto-test-select">
                                     <option value="" disabled>Select Test</option>
                                     @foreach ($labTests as $test)
                                         <option value="{{ $test->id }}" data-price="{{ $test->price }}"
                                             data-category="{{ $test->category }}"
                                             @selected(old('test_id', $labTestOrder->test_id) == $test->id)>
                                             {{ $test->test_name }}</option>
                                     @endforeach
                                 </select>
                                 <div class="fs-sm text-muted mt-1" id="lto-test-info"></div>
                                 <x-admin.error-msg name="test_id" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="lto-date">Order Date</label>
                                 <input type="date" class="form-control" name="order_date"
                                     value="{{ old('order_date', optional($labTestOrder->order_date)->format('Y-m-d')) }}">
                                 <x-admin.error-msg name="order_date" />
                             </div>
                             <div class="col-md-3 mb-4">
                                 <label class="form-label" for="lto-status">Status</label>
                                 <select class="form-select" name="status">
                                     <option value="Pending" @selected(old('status', $labTestOrder->status) == 'Pending')>Pending
                                     </option>
                                     <option value="In Progress" @selected(old('status', $labTestOrder->status) == 'In Progress')>
                                         In Progress</option>
                                     <option value="Completed" @selected(old('status', $labTestOrder->status) == 'Completed')>
                                         Completed</option>
                                     <option value="Cancelled" @selected(old('status', $labTestOrder->status) == 'Cancelled')>
                                         Cancelled</option>
                                 </select>
                                 <x-admin.error-msg name="status" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-12 mb-4">
                                 <label class="form-label" for="lto-result">Result</label>
                                 <textarea class="form-control" name="result" rows="3">{{ old('result', $labTestOrder->result) }}</textarea>
                                 <x-admin.error-msg name="result" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('lab-test-orders.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 Order</button>
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
             const testSelect = document.getElementById('lto-test-select');
             const infoBox = document.getElementById('lto-test-info');

             function showInfo() {
                 const opt = testSelect.selectedOptions[0];
                 if (opt && opt.value) {
                     const price = opt.dataset.price ? `৳${parseFloat(opt.dataset.price).toFixed(2)}` : 'N/A';
                     const category = opt.dataset.category || '-';
                     infoBox.textContent = `Category: ${category} · Price: ${price}`;
                 } else {
                     infoBox.textContent = '';
                 }
             }

             testSelect.addEventListener('change', showInfo);
             showInfo();
         })();
     </script>
 @endsection
