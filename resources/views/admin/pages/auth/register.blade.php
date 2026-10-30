 @extends('admin.layouts.single-master')

 @section('content')
     <main id="main-container">
         <!-- Page Content -->
         <div class="hero-static d-flex align-items-center">
             <div class="content">
                 <div class="row justify-content-center push">
                     <div class="col-md-8 col-lg-6 col-xl-4">
                         <!-- Sign In Block -->
                        
                         <div class="block block-rounded mb-0">
                             <div class="block-header block-header-default">
                                 <h3 class="block-title">Create Account</h3>
                                 <div class="block-options">
                                     <a class="btn-block-option fs-sm" href="{{ route('login') }}">Sign In</a>
                                     
                                 </div>
                             </div>

                             @if (session('success'))
                                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                                     {{ session('success') }}
                                     <button type="button" class="btn-close" data-bs-dismiss="alert"
                                         aria-label="close"></button>
                                 </div>
                             @endif
                             <div class="block-content">
                                 <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
                                     <p class=" h2 mb-1 text-center">welcome to Hospital Mangement</p>
                                     <p class="fw-medium text-muted">
                                         Please fill the following details to create a new account.
                                     </p>


                                     <form class="js-validation-signup" action="{{ route('register') }}" method="POST"
                                         novalidate="novalidate">
                                         @csrf
                                         <div class="py-3">
                                             <div class="mb-4">
                                                 <input type="text" class="form-control form-control-lg form-control-alt"
                                                     id="signup-username" name="name" placeholder="name">
                                             </div>
                                             <div class="mb-4">
                                                 <input type="email" class="form-control form-control-lg form-control-alt"
                                                     id="signup-email" name="email" placeholder="Email">
                                             </div>
                                             <div class="mb-4">
                                                 <input type="password"
                                                     class="form-control form-control-lg form-control-alt"
                                                     id="signup-password" name="password" placeholder="Password">
                                             </div>
                                             <div class="mb-4">
                                                 <input type="password"
                                                     class="form-control form-control-lg form-control-alt"
                                                     id="signup-password-confirm" name="password_confirmation"
                                                     placeholder="Confirm Password">
                                             </div>
                                             
                                         </div>
                                         <div class="row mb-4">
                                             <div class="col-md-6 col-xl-5">
                                                 <button type="submit" class="btn w-100 btn-alt-success">
                                                     <i class="fa fa-fw fa-plus me-1 opacity-50"></i> Sign Up
                                                 </button>
                                             </div>
                                         </div>
                                     </form>
                                     <!-- END Sign In Form -->
                                 </div>
                             </div>
                         </div>
                         <!-- END Sign In Block -->
                     </div>
                 </div>

             </div>
         </div>
         <!-- END Page Content -->
     </main>
 @endsection
