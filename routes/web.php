<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\CR\DashboardController as CRDashboard;
use App\Http\Controllers\Admin\DocumentApprovalController;
use App\Http\Controllers\Student\DocumentController;

/* ================= AUTH ================= */
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ================= DASHBOARD REDIRECT ================= */
Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role_id) {
        1 => redirect()->route('admin.dashboard'),
        2 => redirect()->route('teacher.dashboard'),
        3 => redirect()->route('student.dashboard'),
        4 => redirect()->route('cr.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

/* ================= ADMIN ================= */
Route::middleware(['auth','role:1'])
    ->prefix('admin')
    ->group(function () {

    Route::get('/student-search', 
    [AdminDashboard::class, 'studentSearchForm']
)->name('admin.student.search.form');

Route::get('/student/{id}/edit',
    [AdminDashboard::class, 'editStudent']
)->name('admin.student.edit');

Route::post('/student/{id}/update',
    [AdminDashboard::class, 'updateStudent']
)->name('admin.student.update');

Route::post('/student/{id}/toggle',
    [AdminDashboard::class, 'toggleStudent']
)->name('admin.student.toggle');


Route::post('/student-search',
    [AdminDashboard::class, 'studentSearchResult']
)->name('admin.student.search.result');
Route::get('/search-usn',
    [AdminDashboard::class, 'searchUSN']
)->name('admin.usn.search');




    Route::get('/dashboard',
        [AdminDashboard::class,'index']
    )->name('admin.dashboard');

    Route::get('/faculty-assignments',
        [AdminDashboard::class,'facultyAssignments']
    )->name('admin.faculty.assignments');

    Route::get('/assign-faculty',
        [AdminDashboard::class,'assignFacultyForm']
    )->name('admin.assign.faculty.form');

    Route::post('/assign-faculty',
        [AdminDashboard::class,'assignFacultySave']
    )->name('admin.assign.faculty.save');

    /* AJAX */
    Route::get('/semesters/{course}',
        [AdminDashboard::class,'getSemesters']
    );

    Route::get('/classes/{semester}',
        [AdminDashboard::class,'getClasses']
    );

    Route::get('/subjects/{semester}',
        [AdminDashboard::class,'getSubjects']
    );

    Route::get('/documents',
        [DocumentApprovalController::class,'index']
    )->name('admin.documents');

    Route::get('/documents/{id}/approve',
        [DocumentApprovalController::class,'approve']
    )->name('admin.document.approve');
});

/* ================= TEACHER ================= */
Route::middleware(['auth','role:2'])->group(function () {
    Route::get('/teacher/dashboard',[TeacherDashboard::class,'index'])
        ->name('teacher.dashboard');
});

/* ================= STUDENT ================= */
Route::middleware(['auth','role:3'])->group(function () {
    Route::get('/student/dashboard',[StudentDashboard::class,'index'])
        ->name('student.dashboard');

    Route::get('/student/markscard/pdf',
        [DocumentController::class,'generateMarkscard']
    )->name('student.markscard.pdf');
});

/* ================= CR ================= */
Route::middleware(['auth','role:4'])->group(function () {
    Route::get('/cr/dashboard',[CRDashboard::class,'index'])
        ->name('cr.dashboard');
});
