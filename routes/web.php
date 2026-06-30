<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app'     => 'Smart Clinic API',
        'status'  => 'running',
        'frontend'=> 'http://localhost:5173',
        'admin'   => 'http://localhost:5174',
        'api'     => url('/api'),
    ]);
});
  