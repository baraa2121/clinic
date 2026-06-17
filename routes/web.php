<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('cms.pages.admins.create'); // أو أي صفحة رئيسية تريدها
});

Route::prefix('auth')->middleware('guest:admin,user')->group(function () {
    Route::get('register', [AuthController::class, 'showRegister'])->name('auth.show-register');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('{guard}/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('{guard}/login', [AuthController::class, 'Login'])->name('auth.login');

      Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetEmail'])->name('password.email');
    Route::get('/forgot-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::prefix('/cms')->middleware(['auth:admin,user','verified'])->group(function(){
   Route::resource('appointments',AppointmentController::class,);      //pages
   Route::get('edit-password',[AuthController::class,'editPassword'])->name('auth.edit-password');
   Route::put('update-password',[AuthController::class,'updatePassword'])->name('auth.update-password');
   
});

Route::prefix('/cms')->middleware(['auth:admin,user'])->group(function () {
    Route::resource('admins', AdminController::class);
    Route::resource('users', UserController::class,);      //pages
    Route::resource('doctors', DoctorController::class,);      //pages
    Route::resource('departments', DepartmentController::class,);      //pages
    Route::resource('appointments', AppointmentController::class,);   

    Route::get('edit-password', [AuthController::class, 'editPassword'])->name('auth.edit-password');
    Route::put('update-password', [AuthController::class, 'updatePassword'])->name('auth.update-password');    
    //pages

    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('email-verfication',[AuthController::class,'verificationNotice'])->name('verification.notice');
      Route::get('email-verfication/send',[AuthController::class,'sendVerificationEmail'])->name('verification.send')->middleware('throttle:1,3 ');
      Route::get('email-verfication/{id}/{hash}',[AuthController::class,'verify'])->name('verification.verify');
});
  