<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
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
    ->middleware(['auth:api', 'role:admin'])
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::post('logout',         [AuthController::class, 'logout']);
    Route::put('change-password', [AuthController::class, 'changePassword']);

    // ─── Email Verification ──────────────────────────────────────────────
    Route::get('email-verification/send', [AuthController::class, 'sendVerificationEmail'])
         ->middleware('throttle:1,3');

    // ─── Admins CRUD ─────────────────────────────────────────────────────
    Route::apiResource('admins', AdminController::class);

    // ─── Users CRUD ──────────────────────────────────────────────────────
    Route::apiResource('users', UserController::class)->except(['show', 'create', 'edit']);

    // ─── Doctors CRUD ────────────────────────────────────────────────────
    Route::apiResource('doctors', DoctorController::class);

    // ─── Departments CRUD ────────────────────────────────────────────────
    Route::apiResource('departments', DepartmentController::class);

    // ─── Appointments CRUD ───────────────────────────────────────────────
    Route::apiResource('appointments', AppointmentController::class);

    // ─── Doctor Schedules CRUD ───────────────────────────────────────────
    Route::apiResource('schedules', DoctorScheduleController::class)->except(['show', 'create', 'edit']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::apiResource('notifications', NotificationController::class)->except(['store', 'show', 'create', 'edit']);

    // ─── Patients ────────────────────────────────────────────────────────
    // NOTE: يجب أن يأتي /search قبل /{patient} لتجنب التعارض
    Route::get('patients/search',   [PatientController::class, 'searchPatients']);
    Route::get('patients/{patient}', [PatientController::class, 'show']);

    // ─── Reviews ─────────────────────────────────────────────────────────
    Route::get('reviews',             [ReviewController::class, 'index']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // ─── Payments ────────────────────────────────────────────────────────
    Route::get('payments',              [PaymentController::class, 'index']);
    Route::get('payments/{payment}',    [PaymentController::class, 'show']);
    Route::put('payments/{payment}',    [PaymentController::class, 'update']);
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy']);

    // ─── Services ────────────────────────────────────────────────────────
    Route::get('services',            [ServiceController::class, 'index']);
    Route::get('services/{service}',  [ServiceController::class, 'show']);
    Route::post('services',           [ServiceController::class, 'store']);
    Route::put('services/{service}',  [ServiceController::class, 'update']);
    Route::delete('services/{service}', [ServiceController::class, 'destroy']);

    // ─── Slots ───────────────────────────────────────────────────────────
    Route::get('services/{service}/slots', [SlotController::class, 'index']);
    Route::get('slots/{slot}',             [SlotController::class, 'show']);
    Route::put('slots/{slot}',             [SlotController::class, 'update']);
    Route::delete('slots/{slot}',          [SlotController::class, 'destroy']);

    // ─── Favorites ───────────────────────────────────────────────────────
    Route::get('favorites', [FavoriteController::class, 'adminIndex']);
});

/*
|--------------------------------------------------------------------------
| 🩺 Doctor Routes — للدكتور فقط
|--------------------------------------------------------------------------
*/
Route::prefix('doctor')
    ->middleware(['auth:api', 'role:doctor'])
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::post('logout',         [AuthController::class, 'logout']);
    Route::put('change-password', [AuthController::class, 'changePassword']);

    // ─── Profile (الدكتور يشوف ويعدل بروفايله) ───────────────────────────
    Route::get('me', [DoctorController::class, 'profile']);
    Route::put('updateProfile', [DoctorController::class, 'updateProfile']);

    // ─── My Appointments ─────────────────────────────────────────────────
    Route::get('appointments',               [AppointmentController::class, 'index']);
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::put('appointments/{appointment}', [AppointmentController::class, 'update']);

    // ─── My Schedule ─────────────────────────────────────────────────────
    Route::get('schedules',                [DoctorScheduleController::class, 'index']);
    Route::post('schedules',               [DoctorScheduleController::class, 'store']);
    Route::put('schedules/{schedule}',     [DoctorScheduleController::class, 'update']);
    Route::delete('schedules/{schedule}',  [DoctorScheduleController::class, 'destroy']);

    // ─── Departments (عرض فقط) ───────────────────────────────────────────
    Route::get('departments',               [DepartmentController::class, 'index']);
    Route::get('departments/{department}',  [DepartmentController::class, 'show']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::get('notifications',                    [NotificationController::class, 'index']);
    Route::put('notifications/{notification}',     [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}',  [NotificationController::class, 'destroy']);

    // ─── Doctors (عرض + بحث) ─────────────────────────────────────────────
    // NOTE: يجب أن تأتي /search و /top قبل /{doctor} لتجنب التعارض
    Route::get('doctors/search',   [DoctorController::class, 'search']);
    Route::get('doctors/top',      [DoctorController::class, 'topDoctors']);
    Route::get('doctors',          [DoctorController::class, 'index']);
    Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

    // ─── Patients (الدكتور يشوف ملف مريض معين) ───────────────────────────
    // NOTE: /search قبل /{patient}
    Route::get('patients/search',    [PatientController::class, 'searchPatients']);
    Route::get('patients/{patient}', [PatientController::class, 'show']);

    // ─── Reviews (الدكتور يشوف تقييماته) ─────────────────────────────────
    Route::get('reviews',                   [ReviewController::class, 'index']);
    Route::get('doctors/{doctor}/reviews',  [ReviewController::class, 'doctorReviews']);

    // ─── My Services ─────────────────────────────────────────────────────
    Route::get('services',              [ServiceController::class, 'index']);
    Route::get('services/{service}',    [ServiceController::class, 'show']);
    Route::post('services',             [ServiceController::class, 'store']);
    Route::put('services/{service}',    [ServiceController::class, 'update']);
    Route::delete('services/{service}', [ServiceController::class, 'destroy']);

    // ─── My Slots ────────────────────────────────────────────────────────
    Route::get('services/{service}/slots',  [SlotController::class, 'index']);
    Route::post('services/{service}/slots', [SlotController::class, 'store']);
    Route::get('slots/{slot}',              [SlotController::class, 'show']);
    Route::put('slots/{slot}',              [SlotController::class, 'update']);
    Route::delete('slots/{slot}',           [SlotController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| 👤 User (Patient) Routes — للمريض فقط
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->middleware(['auth:api', 'role:patient'])
    ->group(function () {

    // ─── Auth ────────────────────────────────────────────────────────────
    Route::post('logout',         [AuthController::class, 'logout']);
    Route::put('change-password', [AuthController::class, 'changePassword']);

    // ─── Email Verification ──────────────────────────────────────────────
    Route::get('email-verification/send', [AuthController::class, 'sendVerificationEmail'])
         ->middleware('throttle:1,3');

    // ─── Profile (المريض يشوف ويعدل بروفايله) ────────────────────────────
    Route::get('me', [PatientController::class, 'profile']);
    Route::put('updateProfile', [PatientController::class, 'updateProfile']);

    // ─── Appointments (حجز + عرض + إلغاء) ───────────────────────────────
    Route::get('appointments',                  [AppointmentController::class, 'index']);
    Route::post('appointments',                 [AppointmentController::class, 'store']);
    Route::get('appointments/{appointment}',    [AppointmentController::class, 'show']);
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);

    // ─── Doctors (عرض + بحث) ─────────────────────────────────────────────
    // NOTE: /search و /top قبل /{doctor}
    Route::get('doctors/search',   [DoctorController::class, 'search']);
    Route::get('doctors/top',      [DoctorController::class, 'topDoctors']);
    Route::get('doctors',          [DoctorController::class, 'index']);
    Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

    // ─── Departments (عرض فقط) ───────────────────────────────────────────
    Route::get('departments',              [DepartmentController::class, 'index']);
    Route::get('departments/{department}', [DepartmentController::class, 'show']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::get('notifications',                    [NotificationController::class, 'index']);
    Route::put('notifications/{notification}',     [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}',  [NotificationController::class, 'destroy']);

    // ─── Favorites (المفضلة) ─────────────────────────────────────────────
    Route::get('favorites',              [FavoriteController::class, 'index']);
    Route::post('favorites',             [FavoriteController::class, 'store']);
    Route::delete('favorites/{doctor}',  [FavoriteController::class, 'destroy']);

    // ─── Reviews ─────────────────────────────────────────────────────────
    Route::get('reviews',                  [ReviewController::class, 'index']);
    Route::post('reviews',                 [ReviewController::class, 'store']);
    Route::put('reviews/{review}',         [ReviewController::class, 'update']);
    Route::delete('reviews/{review}',      [ReviewController::class, 'destroy']);
    Route::get('doctors/{doctor}/reviews', [ReviewController::class, 'doctorReviews']);

    // ─── Payments (مدفوعاتي) ─────────────────────────────────────────────
    Route::get('payments',           [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments',          [PaymentController::class, 'store']);

    // ─── Services (عرض خدمات الأطباء) ───────────────────────────────────
    Route::get('services',                       [ServiceController::class, 'index']);
    Route::get('services/{service}',             [ServiceController::class, 'show']);
    Route::get('doctors/{doctor}/services',      [ServiceController::class, 'doctorServices']);

    // ─── Slots (عرض السلوتات المتاحة) ───────────────────────────────────
    Route::get('services/{service}/slots', [SlotController::class, 'index']);
    Route::get('slots/{slot}',             [SlotController::class, 'show']);
});
