<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.layouts.master');
});

Route::get('/dashboard', function () {
    return view('admin.pages.dashboard');
});

Route::get('/login', function () {
    return view('admin.pages.auth.login');
});


// Route::get('/users', function () {
//     return view('admin.pages.user.index');
// });

// Route::get('/users', [UserController::class, 'index'])->name('users.index');
// Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
// Route::post('/users', [Usercontroller::class, 'store'])->name('users.store');
// Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
// Route::get('/users/{id}/edit', [Usercontroller::class, 'edit'])->name('users.edit');
// Route::put('/users/{id}', [Usercontroller::class, 'update'])->name('users.update');
// Route::delete('/users/{user}', [Usercontroller::class, 'destroy'])->name('users.destroy');


Route::resource('users', Usercontroller::class);
Route::resource('patients', PatientController::class);
Route::resource('doctors', DoctorController::class);
Route::resource('appointments', AppointmentController::class);
Route::resource('prescriptions', PrescriptionController::class);