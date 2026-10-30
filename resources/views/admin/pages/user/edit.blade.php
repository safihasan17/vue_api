 @extends('admin.layouts.master')

 @section('title', 'users - Edit')

 @section('content')

     <div class="block block-rounded block-transparent mt-5">

         <div class="block-content fs-sm mt-2">
             <x-admin.phead title="users edit" subtitle="Edit user from this section">

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
                     <form action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-name">Full Name</label>
                                 <input type="text" class="form-control" name="name" placeholder="e.g. Sumaiya Islam" value= "{{ $user->name }}">
                                 <x-admin.error-msg name="name" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-email">Email</label>
                                 <input type="email" class="form-control" name="email" value="{{ $user->email }}">
                                 <x-admin.error-msg name="email" />
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-role">Role</label>
                                 <select class="form-select" name="role_id">
                                     <option value="0"  disabled>Select a Role</option>
                                     @foreach ($roles as $item)
                                         <option value="{{ $item->id }}" @selected($user->role_id == $item->id)>
                                             {{ $item->name }}</option>
                                     @endforeach

                                 </select>
                                 <x-admin.error-msg name="role_id" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <label class="form-label" for="us-phone">Phone</label>
                                 <input type="text" class="form-control" name="phone" placeholder="017XX-XXXXXX" value="{{ $user->phone }}">
                                 <x-admin.error-msg name="phone" />
                             </div>
                             <div class="col-md-6 mb-4">
                                 <div class="form-switch-custom">
                                     <input class="form-switch-input-custom" type="checkbox" id="switchOne" {{$user->active==1 ? 'checked' : ''}}
                                         name="active">
                                     <label class="form-switch-label" for="switchOne">Active</label>
                                 </div>
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
