<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| الراوتس مقسمة إلى:
|   1. Guest  → متاح للجميع (تسجيل، دخول، كلمة مرور)
|   2. Admin  → مخصص للأدمن فقط
|   3. Doctor → مخصص للدكتور فقط
|   4. User   → مخصص للمريض/اليوزر فقط
|   5. Shared → مشترك بين أكثر من رول
|
*/

/*
|--------------------------------------------------------------------------
| 🔓 Guest Routes — لا تحتاج تسجيل دخول
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'requestPasswordReset']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| 🔐 Admin Routes — للأدمن فقط
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware('auth:api','role:admin')
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::post('logout',          [ApiAuthController::class, 'logout']);
    Route::post('update-profile',  [ApiAuthController::class, 'updateProfile']);
    Route::put('update-password',  [AuthController::class, 'updatePassword']);

    // ─── Email Verification ──────────────────────────────────────────────
    Route::get('email-verification/send',         [AuthController::class, 'sendVerificationEmail'])->middleware('throttle:1,3');

    // ─── Admins CRUD ─────────────────────────────────────────────────────
    // index, store, show, update, destroy
    Route::apiResource('admins', AdminController::class);

    // ─── Users CRUD ──────────────────────────────────────────────────────
    // index, store, update, destroy (no show/create/edit)
    Route::apiResource('users', UserController::class)->except(['show', 'create', 'edit']);

    // ─── Doctors CRUD ────────────────────────────────────────────────────
    // index, store, show, update, destroy
    Route::apiResource('doctors', DoctorController::class);

    // ─── Departments CRUD ────────────────────────────────────────────────
    // index, store, show, update, destroy
    Route::apiResource('departments', DepartmentController::class);

    // ─── Appointments CRUD ───────────────────────────────────────────────
    // index, store, show, update, destroy
    Route::apiResource('appointments', AppointmentController::class);

    // ─── Doctor Schedules CRUD ───────────────────────────────────────────
    // index, store, update, destroy (no show/create/edit)
    Route::apiResource('schedules', DoctorScheduleController::class)->except(['show', 'create', 'edit']);

    // ─── Notifications ───────────────────────────────────────────────────
    // index, update, destroy (no store/show/create/edit)
    Route::apiResource('notifications', NotificationController::class)->except(['store', 'show', 'create', 'edit']);

    // ─── Patients ────────────────────────────────────────────────────────
    Route::get('patients/{patient}',         [PatientController::class, 'profile']);
    Route::get('patients/search',            [PatientController::class, 'searchPatients']);
    Route::put('patients/{patient}/profile', [PatientController::class, 'updateProfile']);
});

/*
|--------------------------------------------------------------------------
| 🩺 Doctor Routes — للدكتور فقط
|--------------------------------------------------------------------------
*/
Route::prefix('doctor')
    ->middleware('auth:api','role:doctor')
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::post('logout',          [ApiAuthController::class, 'logout']);
    Route::post('update-profile',  [ApiAuthController::class, 'updateProfile']);
    Route::put('update-password',  [AuthController::class, 'updatePassword']);

    // ─── My Appointments (الدكتور يشوف مواعيده ويعدل عليها) ─────────────
    Route::get('appointments',                        [AppointmentController::class, 'index']);
    Route::get('appointments/{appointment}',          [AppointmentController::class, 'show']);
    Route::put('appointments/{appointment}',          [AppointmentController::class, 'update']);

    // ─── My Schedule ─────────────────────────────────────────────────────
    Route::get('schedules',                           [DoctorScheduleController::class, 'index']);
    Route::post('schedules',                          [DoctorScheduleController::class, 'store']);
    Route::put('schedules/{schedule}',                [DoctorScheduleController::class, 'update']);
    Route::delete('schedules/{schedule}',             [DoctorScheduleController::class, 'destroy']);

    // ─── Departments (عرض فقط) ───────────────────────────────────────────
    Route::get('departments',                         [DepartmentController::class, 'index']);
    Route::get('departments/{department}',            [DepartmentController::class, 'show']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::get('notifications',                       [NotificationController::class, 'index']);
    Route::put('notifications/{notification}',        [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}',     [NotificationController::class, 'destroy']);

    // ─── Doctors (عرض + بحث) ─────────────────────────────────────────────
    Route::get('doctors',                             [DoctorController::class, 'index']);
    Route::get('doctors/search',                      [DoctorController::class, 'search']);
    Route::get('doctors/top',                         [DoctorController::class, 'topDoctors']);
    Route::get('doctors/{doctor}',                    [DoctorController::class, 'show']);

    // ─── Patients (الدكتور يشوف ملف المريض) ──────────────────────────────
    Route::get('patients/{patient}',                  [PatientController::class, 'profile']);
    Route::get('patients/search',                     [PatientController::class, 'searchPatients']);
});

/*
|--------------------------------------------------------------------------
| 👤 User (Patient) Routes — للمريض فقط
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->middleware('auth:api','role:user')
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::get('me',               fn (Request $request) => response()->json($request->user()));
    Route::post('logout',          [ApiAuthController::class, 'logout']);
    Route::post('update-profile',  [ApiAuthController::class, 'updateProfile']);
    Route::put('update-password',  [AuthController::class, 'updatePassword']);

    // ─── Email Verification ──────────────────────────────────────────────
    Route::get('email-verification/send',         [AuthController::class, 'sendVerificationEmail'])->middleware('throttle:1,3');

    // ─── Patient Profile ─────────────────────────────────────────────────
    Route::get('profile/{patient}',              [PatientController::class, 'profile']);
    Route::put('profile/{patient}',              [PatientController::class, 'updateProfile']);

    // ─── Appointments (حجز + عرض + إلغاء) ───────────────────────────────
    Route::get('appointments',                   [AppointmentController::class, 'index']);
    Route::post('appointments',                  [AppointmentController::class, 'store']);
    Route::get('appointments/{appointment}',     [AppointmentController::class, 'show']);
    Route::delete('appointments/{appointment}',  [AppointmentController::class, 'destroy']);

    // ─── Doctors (عرض + بحث) ─────────────────────────────────────────────
    Route::get('doctors',                        [DoctorController::class, 'index']);
    Route::get('doctors/search',                 [DoctorController::class, 'search']);
    Route::get('doctors/top',                    [DoctorController::class, 'topDoctors']);
    Route::get('doctors/{doctor}',               [DoctorController::class, 'show']);

    // ─── Departments (عرض فقط) ───────────────────────────────────────────
    Route::get('departments',                    [DepartmentController::class, 'index']);
    Route::get('departments/{department}',       [DepartmentController::class, 'show']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::get('notifications',                  [NotificationController::class, 'index']);
    Route::put('notifications/{notification}',   [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}',[NotificationController::class, 'destroy']);



});
