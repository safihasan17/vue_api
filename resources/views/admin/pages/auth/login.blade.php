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
                                <h3 class="block-title">Sign In</h3>

                                <div class="block-options">
                                    <a class="btn-block-option fs-sm" href="op_auth_reminder.html">
                                        Forgot Password?
                                    </a>

                                    <a class="btn-block-option"
                                       href="{{ route('register') }}"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="left"
                                       title="New Account">
                                        <i class="fa fa-user-plus"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="alert"
                                            aria-label="close"></button>
                                </div>
                            @endif

                            <div class="block-content">
                                <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">

                                    <p class="h2 mb-1 text-center">
                                        Welcome to Hospital Management
                                    </p>

                                    <p class="fw-medium text-muted">
                                        Please login in to access your dashboard
                                    </p>

                                    {{-- Role Login Buttons --}}
                                    <div class="my-4 d-flex gap-2 justify-content-center flex-wrap">

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="hoppe.jo@example.net">
                                             Admin
                                        </button>

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="amoore@example.org">
                                            Doctor
                                        </button>

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="cristina.zboncak@example.com">
                                            Receptionist
                                        </button>

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="fchamplin@example.org">
                                            Pharmacist
                                        </button>

                                        {{-- <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="larson.kyla@example.net">
                                            Lab Technician
                                        </button>

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="wturner@example.net">
                                            Accountant
                                        </button>

                                        <button type="button"
                                                class="btn btn-primary role-login-btn"
                                                data-email="nurse@example.com">
                                            Nurse
                                        </button> --}}

                                    </div>


                                    {{-- Login Form --}}
                                    <form class="js-validation-signin"
                                          action="{{ route('login.store') }}"
                                          method="POST"
                                          novalidate>

                                        @csrf

                                        <div class="py-3">

                                            {{-- Email --}}
                                            <div class="mb-4">
                                                <input type="email"
                                                       class="form-control form-control-alt form-control-lg"
                                                       id="login-username"
                                                       name="email"
                                                       placeholder="example@gmail.com"
                                                       value="admin@example.com">
                                            </div>

                                            {{-- Password --}}
                                            <div class="mb-4">
                                                <input type="password"
                                                       class="form-control form-control-alt form-control-lg"
                                                       id="login-password"
                                                       name="password"
                                                       placeholder="Password"
                                                       value="password">
                                            </div>

                                            {{-- Remember Me --}}
                                            <div class="mb-4">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           value="1"
                                                           id="login-remember"
                                                           name="login-remember">

                                                    <label class="form-check-label"
                                                           for="login-remember">
                                                        Remember Me
                                                    </label>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6 col-xl-5">
                                                <button type="submit"
                                                        class="btn w-100 btn-alt-primary">
                                                    <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                                                    Sign In
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


@section('script')
<script>
    document.querySelectorAll('.role-login-btn').forEach(button => {

        button.addEventListener('click', function () {

            document.querySelector('#login-username').value =
                this.dataset.email;

            
            document.querySelector('#login-password').value = 'password';

        });

    });
</script>
@endsection