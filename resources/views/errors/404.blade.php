@extends('admin.layouts.single-master')

@section('content')

<main id="main-container">
    <!-- Page Content -->
    <div class="hero">
        <div class="hero-inner text-center">
            <div class="bg-body-extra-light">
                <div class="content content-full overflow-hidden">
                    <div class="py-4">
                        <!-- Error Header -->
                        <h1 class="display-1 fw-bolder text-city">
                            404
                        </h1>
                        <h2 class="h4 fw-normal text-muted mb-5">
                            We are sorry but the page you are looking for was not found..
                        </h2>
                        <!-- END Error Header -->


                    </div>
                </div>
            </div>
            <div class="content content-full text-muted fs-sm fw-medium">
                <!-- Error Footer -->
                <p class="mb-1">
                    Would you like to let us know about it?
                </p>
                <a class="link-fx" href="javascript:void(0)">Report it</a> or <a class="link-fx"
                    href="be_pages_error_all.html">Go Back to Dashboard</a>
                <!-- END Error Footer -->
            </div>
        </div>
    </div>
    <!-- END Page Content -->
</main>

@endsection
