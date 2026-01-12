<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\CR\DashboardController as CRDashboard;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard Resolver
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $role = auth()->user()->role_id;

    return match ($role) {
        1 => redirect()->route('admin.dashboard'),
        2 => redirect()->route('teacher.dashboard'),
        3 => redirect()->route('student.dashboard'),
        4 => redirect()->route('cr.dashboard'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Role Based Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:1'])->get(
    '/admin/dashboard',
    [AdminDashboard::class, 'index']
)->name('admin.dashboard');

Route::middleware(['auth','role:2'])->get(
    '/teacher/dashboard',
    [TeacherDashboard::class, 'index']
)->name('teacher.dashboard');

Route::middleware(['auth','role:3'])->get(
    '/student/dashboard',
    [StudentDashboard::class, 'index']
)->name('student.dashboard');

Route::middleware(['auth','role:4'])->get(
    '/cr/dashboard',
    [CRDashboard::class, 'index']
)->name('cr.dashboard');
