 @extends('admin.layouts.master')

 @section('title', 'users - Create')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="users create" subtitle="create user from this section">

                 <a href="{{ route('users.index') }}" type="button" class="btn btn-sm btn-primary">
                     <i class="fa fa-plus opacity-50 me-1"></i> back to users
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
                     <form action="{{ route('users.store') }}" method="POST">
                         @csrf
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-name">Full Name</label>
                                 <input type="text" class="form-control" name="name" placeholder="e.g. Sumaiya Islam">
                                 <x-admin.error-msg name="name" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-email">Email</label>
                                 <input type="email" class="form-control" name="email" placeholder="name@medicare.bd">
                                 <x-admin.error-msg name="email" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-role">Role</label>
                                 <select class="form-select" name="role_id">
                                     <option value="0" selected disabled>Select a Role</option>
                                     @foreach ($roles as $item)
                                         <option value="{{ $item->id }}" @selected(old('role_id') == $item->id)>
                                             {{ $item->name }}</option>
                                     @endforeach

                                 </select>
                                 <x-admin.error-msg name="role" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-phone">Phone</label>
                                 <input type="text" class="form-control" name="phone" placeholder="017XX-XXXXXX">
                                 <x-admin.error-msg name="phone" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <div class="form-switch-custom">
                                     <input class="form-switch-input-custom" type="checkbox" id="switchOne" checked=""
                                         name="active">
                                     <label class="form-switch-label" for="switchOne">Active</label>
                                 </div>
                             </div>
                         </div>
                         <div class="row">
                             <div class=" col-12 col-md-6 mb-4">
                                 <label class="form-label" for="us-pass">Password</label>
                                 <input type="password" class="form-control" name="password"
                                     placeholder="Leave blank to keep unchanged">
                                 <x-admin.error-msg name="password" />
                             </div>
                             <div class=" col-12 col-md-6 mb-4">
                                 <label class="form-label" for="us-pass2">Confirm Password</label>
                                 <input type="password" class="form-control" name="password_confirmation">
                                 <x-admin.error-msg name="password_confirmation" />
                             </div>
                         </div>

                         <div class="block-content block-content-full text-end bg-body">
                             <a href="{{ route('users.index') }}" class="btn btn-sm btn-alt-secondary me-1">
                                 Cancel
                             </a>
                             <button type="submit" class="btn btn-sm btn-primary">Save
                                 User</button>
                         </div>
                     </form>
                 </div>
             </div>

         </div>

     </div>

 @endsection
