<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\CR\DashboardController as CRDashboard;
use App\Http\Controllers\Admin\DocumentApprovalController;
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
Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role_id) {
        1 => redirect()->route('admin.dashboard'),
        2 => redirect()->route('teacher.dashboard'),
        3 => redirect()->route('student.dashboard'),
        4 => redirect()->route('cr.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Role Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:1'])->get('/admin/dashboard',[AdminDashboard::class,'index'])->name('admin.dashboard');
Route::middleware(['auth','role:2'])->get('/teacher/dashboard',[TeacherDashboard::class,'index'])->name('teacher.dashboard');
Route::middleware(['auth','role:3'])->get('/student/dashboard',[StudentDashboard::class,'index'])->name('student.dashboard');
Route::middleware(['auth','role:4'])->get('/cr/dashboard',[CRDashboard::class,'index'])->name('cr.dashboard');



Route::middleware(['auth','role:1'])->group(function () {
    Route::get('/admin/documents', [DocumentApprovalController::class, 'index'])
        ->name('admin.documents');

    Route::get('/admin/documents/{id}/approve', [DocumentApprovalController::class, 'approve'])
        ->name('admin.document.approve');
});

Route::middleware(['auth','role:2'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboard::class, 'index'])
        ->name('teacher.dashboard');

    Route::post('/teacher/attendance', [TeacherDashboard::class, 'markAttendance'])
        ->name('teacher.attendance');

    Route::post('/teacher/marks', [TeacherDashboard::class, 'saveMarks'])
        ->name('teacher.marks');
});

Route::middleware(['auth','role:4'])->group(function () {
    Route::get('/cr/dashboard', [CRDashboard::class, 'index'])
        ->name('cr.dashboard');

    Route::post('/cr/confirm', [CRDashboard::class, 'confirmChapter'])
        ->name('cr.confirm');
});


Route::middleware(['auth','role:3'])->get(
    '/student/dashboard',
    [StudentDashboard::class, 'index']
)->name('student.dashboard');


use App\Http\Controllers\Student\DocumentController;

Route::middleware(['auth','role:3'])->get(
    '/student/markscard/pdf',
    [DocumentController::class,'generateMarkscard']
)->name('student.markscard.pdf');
