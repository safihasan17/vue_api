<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\LabTestOrderController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicinePurchaseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SslcommerzController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.pages.auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth', 'role_id:1')->group(function () {

    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');

    Route::resource('users', UserController::class);
    // Only admin can delete roles
    Route::resource('roles', RoleController::class)->only(['destroy']);
    Route::resource('patients', PatientController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::resource('prescriptions', PrescriptionController::class);
    Route::resource('lab-test-orders', LabTestOrderController::class);
    Route::resource('wards', WardController::class);
    Route::resource('beds', BedController::class);
    Route::resource('admissions', AdmissionController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('invoice-items', InvoiceItemController::class);

    Route::post('prescriptions/{prescription}/generate-invoice', [PrescriptionController::class, 'generateInvoice'])
        ->name('prescriptions.generate-invoice');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------------- 2) ADMIN + DOCTOR: appointment & prescription banano/edit ----------------
Route::middleware('auth', 'role_id:1,2')->group(function () {

    Route::resource('appointments', AppointmentController::class)->except(['index', 'show']);
    Route::resource('prescriptions', PrescriptionController::class)->except(['index', 'show']);
});

// ---------------- 3) ADMIN + RECEPTIONIST: patient, admission, test, invoice ----------------
Route::middleware('auth', 'role_id:1,3')->group(function () {

    // Roles: Admin + Receptionist can list, view, create and update (delete is admin only)
    Route::resource('roles', RoleController::class)->except(['destroy']);

    Route::resource('patients', PatientController::class)->except(['show', 'destroy']);
    Route::resource('admissions', AdmissionController::class)->except(['destroy']);
    Route::resource('lab-test-orders', LabTestOrderController::class)->except(['destroy']);
    Route::resource('invoices', InvoiceController::class)->except(['destroy']);
    Route::resource('invoice-items', InvoiceItemController::class)->except(['destroy']);

    Route::post('prescriptions/{prescription}/generate-invoice', [PrescriptionController::class, 'generateInvoice'])
        ->name('prescriptions.generate-invoice');
    Route::post('appointments/{appointment}/generate-invoice', [InvoiceController::class, 'fromAppointment'])
        ->name('appointments.generate-invoice');
    Route::post('lab-test-orders/{labTestOrder}/generate-invoice', [LabTestOrderController::class, 'generateInvoice'])
        ->name('lab-test-orders.generate-invoice');
});

// ---------------- 4) ADMIN + DOCTOR + RECEPTIONIST: 
Route::middleware('auth', 'role_id:1,2,3')->group(function () {

    Route::resource('appointments', AppointmentController::class);
    Route::resource('prescriptions', PrescriptionController::class)->only(['index', 'show']);
    Route::resource('patients', PatientController::class)->only(['show']);

    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
   
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
])->group(function () {
    Route::match(['get', 'post'], 'payments/sslcommerz/success', [SslcommerzController::class, 'success'])->name('sslcommerz.success');
    Route::match(['get', 'post'], 'payments/sslcommerz/fail', [SslcommerzController::class, 'fail'])->name('sslcommerz.fail');
    Route::match(['get', 'post'], 'payments/sslcommerz/cancel', [SslcommerzController::class, 'cancel'])->name('sslcommerz.cancel');
    Route::post('payments/sslcommerz/ipn', [SslcommerzController::class, 'ipn'])->name('sslcommerz.ipn');
});

// Admin + Receptionist: take a payment (counter or online)
Route::middleware('auth', 'role_id:1,3')->group(function () {
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::post('invoices/{invoice}/pay-online', [SslcommerzController::class, 'initiate'])->name('invoices.pay-online');
});

// Admin only: see / edit / delete payments
Route::middleware('auth', 'role_id:1')->group(function () {
    Route::resource('payments', PaymentController::class)->only(['index', 'edit', 'update', 'destroy']);
});

// ---------------- PHARMACY: ADMIN + PHARMACIST (role 4): medicines, suppliers, purchases ----------------
Route::middleware('auth', 'role_id:1,4')->group(function () {

    Route::resource('medicines', MedicineController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('medicine-purchases', MedicinePurchaseController::class);
});

// Pharmacist needs their own profile page
Route::middleware('auth', 'role_id:4')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
