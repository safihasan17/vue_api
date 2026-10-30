<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\LabTestOrderController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionMedicineController;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceController;
use App\Http\Controllers\Api\MedicinePurchaseController as ApiMedicinePurchaseController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\RoleController as ApiRoleController;
use App\Http\Controllers\Api\SupplierController as ApiSupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// ---- Public auth endpoints ----
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// ---- Everything below needs "Authorization: Bearer <token>" ----
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('roles', ApiRoleController::class);

    Route::get('doctors/search', [DoctorController::class, 'search']);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('departments', DepartmentController::class);


    Route::apiResource('appointments', AppointmentController::class);
    // age eta
    Route::get('prescriptions/options', [PrescriptionController::class, 'options']);
    Route::post('prescriptions/{id}/generate-invoice', [PrescriptionController::class, 'generateInvoice']);
    Route::apiResource('prescriptions', PrescriptionController::class);

    Route::apiResource('lab-tests', LabTestController::class);

    Route::get('lab-test-orders/options', [LabTestOrderController::class, 'options']);
    Route::post('lab-test-orders/{id}/generate-invoice', [LabTestOrderController::class, 'generateInvoice']);
    Route::apiResource('lab-test-orders', LabTestOrderController::class);
    Route::apiResource('medicines', MedicineController::class);
    Route::apiResource('prescription-medicines', PrescriptionMedicineController::class);



    Route::get('wards/options', [WardController::class, 'options']);
    Route::apiResource('wards', WardController::class);

    Route::apiResource('beds', BedController::class);

    Route::get('admissions/options', [AdmissionController::class, 'options']);
    Route::apiResource('admissions', AdmissionController::class);

    // ---- Billing ----
    Route::get('invoices/options', [ApiInvoiceController::class, 'options']);
    Route::apiResource('invoices', ApiInvoiceController::class);
    Route::post('appointments/{id}/generate-invoice', [ApiInvoiceController::class, 'fromAppointment']);
    Route::apiResource('payments', ApiPaymentController::class);

    // ---- Pharmacy purchasing ----
    Route::apiResource('suppliers', ApiSupplierController::class);
    Route::get('medicine-purchases/options', [ApiMedicinePurchaseController::class, 'options']);
    Route::apiResource('medicine-purchases', ApiMedicinePurchaseController::class);
});
