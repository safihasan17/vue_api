<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header">
        <!-- Logo -->
        <a class="fw-semibold text-dual" href="index.html">
            <span class="smini-visible">
                <i class="fa fa-circle-notch text-primary"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">OneUI</span>
        </a>
        <!-- END Logo -->

        <!-- Extra -->
        <div class="d-flex align-items-center gap-1">
            <!-- Dark Mode -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-dark-mode-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end smini-hide border-0"
                    aria-labelledby="sidebar-dark-mode-dropdown">
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_off" data-dark-mode="off">
                        <i class="far fa-sun fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Light</span>
                    </button>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_on" data-dark-mode="on">
                        <i class="far fa-moon fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Dark</span>
                    </button>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout"
                        data-action="dark_mode_system" data-dark-mode="system">
                        <i class="fa fa-desktop fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">System</span>
                    </button>
                </div>
            </div>
            <!-- END Dark Mode -->

            <!-- Options -->
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-themes-dropdown"
                    data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-brush"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end fs-sm smini-hide border-0"
                    aria-labelledby="sidebar-themes-dropdown">
                    <!-- Color Themes -->
                    <!-- Layout API, functionality initialized in Template._uiHandleTheme() -->
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="default">
                        <span>Default</span>
                        <i class="fa fa-circle text-default"></i>
                    </button>
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="assets/css/themes/amethyst.min.css">
                        <span>Amethyst</span>
                        <i class="fa fa-circle text-amethyst"></i>
                    </button>
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="assets/css/themes/city.min.css">
                        <span>City</span>
                        <i class="fa fa-circle text-city"></i>
                    </button>
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="assets/css/themes/flat.min.css">
                        <span>Flat</span>
                        <i class="fa fa-circle text-flat"></i>
                    </button>
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="assets/css/themes/modern.min.css">
                        <span>Modern</span>
                        <i class="fa fa-circle text-modern"></i>
                    </button>
                    <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium"
                        data-toggle="theme" data-theme="assets/css/themes/smooth.min.css">
                        <span>Smooth</span>
                        <i class="fa fa-circle text-smooth"></i>
                    </button>
                    <!-- END Color Themes -->

                    <div class="dropdown-divider d-dark-none"></div>

                    <!-- Sidebar Styles -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout"
                        data-action="sidebar_style_light" href="javascript:void(0)">
                        <span>Sidebar Light</span>
                    </a>
                    <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="sidebar_style_dark"
                        href="javascript:void(0)">
                        <span>Sidebar Dark</span>
                    </a>
                    <!-- END Sidebar Styles -->

                    <div class="dropdown-divider d-dark-none"></div>

                    <!-- Header Styles -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="header_style_light"
                        href="javascript:void(0)">
                        <span>Header Light</span>
                    </a>
                    <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="header_style_dark"
                        href="javascript:void(0)">
                        <span>Header Dark</span>
                    </a>
                    <!-- END Header Styles -->
                </div>
            </div>
            <!-- END Options -->

            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close"
                href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
            <!-- END Close Sidebar -->
        </div>
        <!-- END Extra -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        @if (auth()->user()->role_id == 1)
            <div class="content-side">
                <ul class="nav-main">

                    <li class="nav-main-heading">Hospital Management</li>

                    <li class="nav-main-item">
                        <a class="nav-main-link active" href="{{ route('dashboard') }}">
                            <i class="nav-main-link-icon fa fa-heartbeat"></i>
                            <span class="nav-main-link-name">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('patients.index') }}">
                            <i class="nav-main-link-icon fa fa-user-injured"></i>
                            <span class="nav-main-link-name">Patients</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('doctors.index') }}">
                            <i class="nav-main-link-icon fa fa-user-md"></i>
                            <span class="nav-main-link-name">Doctors</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('appointments.index') }}">
                            <i class="nav-main-link-icon fa fa-calendar-check"></i>
                            <span class="nav-main-link-name">Appointments</span>
                        </a>
                    </li>

                    {{-- Admission (toggle menu) --}}
                    <li
                        class="nav-main-item {{ request()->routeIs('wards.*', 'beds.*', 'admissions.*') ? 'open' : '' }}">
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('wards.*', 'beds.*', 'admissions.*') ? 'true' : 'false' }}"
                            href="#">
                            <i class="nav-main-link-icon fa fa-procedures"></i>
                            <span class="nav-main-link-name">Admission</span>
                        </a>
                        <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('wards.index') }}">
                                    <span class="nav-main-link-name">Wards</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('beds.index') }}">
                                    <span class="nav-main-link-name">Beds</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('admissions.index') }}">
                                    <span class="nav-main-link-name">Admission</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('prescriptions.index') }}">
                            <i class="nav-main-link-icon fa fa-file-medical"></i>
                            <span class="nav-main-link-name">Prescriptions</span>
                        </a>
                    </li>

                    {{-- Pharmacy (toggle menu) --}}
                    <li
                        class="nav-main-item {{ request()->routeIs('medicines.*', 'suppliers.*', 'medicine-purchases.*') ? 'open' : '' }}">
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                            aria-expanded="{{ request()->routeIs('medicines.*', 'suppliers.*', 'medicine-purchases.*') ? 'true' : 'false' }}"
                            href="#">
                            <i class="nav-main-link-icon fa fa-pills"></i>
                            <span class="nav-main-link-name">Pharmacy</span>
                        </a>
                        <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('medicines.index') }}">
                                    <span class="nav-main-link-name">Medicines</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('suppliers.index') }}">
                                    <span class="nav-main-link-name">Suppliers</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('medicine-purchases.index') }}">
                                    <span class="nav-main-link-name">Medicine Purchases</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('lab-test-orders.index') }}">
                            <i class="nav-main-link-icon fa fa-flask"></i>
                            <span class="nav-main-link-name">Lab Tests</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('invoices.index') }}">
                            <i class="nav-main-link-icon fa fa-file-invoice-dollar"></i>
                            <span class="nav-main-link-name">Billing / Invoices</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('payments.index') }}">
                            <i class="nav-main-link-icon fa fa-money-bill-wave"></i>
                            <span class="nav-main-link-name">Payments</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('invoice-items.index') }}">
                            <i class="nav-main-link-icon fa fa-file-invoice-dollar"></i>
                            <span class="nav-main-link-name">Invoice Item</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="/users">
                            <i class="nav-main-link-icon fa fa-users"></i>
                            <span class="nav-main-link-name">Users</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('roles.index') }}">
                            <i class="nav-main-link-icon fa fa-user-shield"></i>
                            <span class="nav-main-link-name">Role</span>
                        </a>
                    </li>

                </ul>
            </div>
        @endif

        {{-- doctor sidebar --}}

        @if (auth()->user()->role_id == 2)
            <li class="nav-main-heading">Hospital Management</li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('appointments.index') }}">
                    <i class="nav-main-link-icon fa fa-user-md"></i>
                    <span class="nav-main-link-name">Appointments</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('prescriptions.index') }}">
                    <i class="nav-main-link-icon fa fa-file-medical"></i>
                    <span class="nav-main-link-name">Prescriptions</span>
                </a>
            </li>
        @endif


        @if (auth()->user()->role_id == 3)
            <li class="nav-main-heading">Hospital Management</li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('patients.index') }}">
                    <i class="nav-main-link-icon fa fa-user-injured"></i>
                    <span class="nav-main-link-name">Patients</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('appointments.index') }}">
                    <i class="nav-main-link-icon fa fa-user-md"></i>
                    <span class="nav-main-link-name">Appointments</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('admissions.index') }}">
                    <i class="nav-main-link-icon fa fa-procedures"></i>
                    <span class="nav-main-link-name">Admission</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('prescriptions.index') }}">
                    <i class="nav-main-link-icon fa fa-file-medical"></i>
                    <span class="nav-main-link-name">Prescriptions</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('lab-test-orders.index') }}">
                    <i class="nav-main-link-icon fa fa-flask"></i>
                    <span class="nav-main-link-name">Lab Tests</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('invoices.index') }}">
                    <i class="nav-main-link-icon fa fa-file-invoice-dollar"></i>
                    <span class="nav-main-link-name">Billing / Invoices</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('roles.index') }}">
                    <i class="nav-main-link-icon fa fa-user-shield"></i>
                    <span class="nav-main-link-name">Role</span>
                </a>
            </li>
        @endif

        {{-- pharmacist sidebar --}}

        @if (auth()->user()->role_id == 4)
            <li class="nav-main-heading">Pharmacy</li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('medicines.index') }}">
                    <i class="nav-main-link-icon fa fa-pills"></i>
                    <span class="nav-main-link-name">Pharmacy / Medicines</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('suppliers.index') }}">
                    <i class="nav-main-link-icon fa fa-truck"></i>
                    <span class="nav-main-link-name">Suppliers</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('medicine-purchases.index') }}">
                    <i class="nav-main-link-icon fa fa-boxes"></i>
                    <span class="nav-main-link-name">Medicine Purchases</span>
                </a>
            </li>
        @endif
        <!-- END Side Navigation -->
    </div>
    <!-- END Sidebar Scrolling -->
</nav>
