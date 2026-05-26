<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LawyerCaseController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\WelcomeController;
use App\Models\Lawyer;
use Inertia\Inertia;

// Public Routes
Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/browse', [LawyerController::class, 'index'])->name('lawyers.browse');
Route::get('/lawyers/{lawyer:slug}', [LawyerController::class, 'show'])->name('lawyers.show');
Route::get('/cases/{case:slug}', [LawyerCaseController::class, 'show'])->name('cases.show');

// Authenticated Lawyer Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/moderator/lawyers/{lawyer}/toggle-certified', [DashboardController::class, 'toggleCertified'])->name('moderator.lawyers.toggle-certified');

    Route::get('/profile/lawyer', [DashboardController::class, 'myProfiles'])->name('lawyer.profile-root');

    Route::get('/profile/lawyer/{lawyer:slug}', [LawyerController::class, 'editAssigned'])->name('lawyer.edit');
    Route::post('/profile/lawyer/{lawyer:slug}/update', [LawyerController::class, 'updateAssigned'])->name('lawyer.update');
});


// Admin-Only Routes
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/audit-logs', [AdminController::class, 'auditLogsIndex'])->name('admin.audit-logs.index');
    Route::get('/admin/lawyers', [AdminController::class, 'lawyersIndex'])->name('admin.lawyers.index');
    Route::post('/admin/lawyers', [AdminController::class, 'store'])->name('admin.lawyers.store');
    Route::post('/admin/lawyers/{lawyer}', [AdminController::class, 'update'])->name('admin.lawyers.update');
    Route::post('/admin/lawyers/{lawyer}/delete', [AdminController::class, 'destroy'])->name('admin.lawyers.destroy');

    // Admin Cases Management Routes
    Route::get('/admin/cases', [AdminController::class, 'casesIndex'])->name('admin.cases.index');
    Route::post('/admin/cases', [AdminController::class, 'caseStore'])->name('admin.cases.store');
    Route::post('/admin/cases/{case}', [AdminController::class, 'caseUpdate'])->name('admin.cases.update');
    Route::post('/admin/cases/{case}/delete', [AdminController::class, 'caseDestroy'])->name('admin.cases.destroy');

    // Admin User Management Routes
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminController::class, 'userShow'])->name('admin.users.show');
    Route::post('/admin/users', [AdminController::class, 'userStore'])->name('admin.users.store');
    Route::post('/admin/users/{user}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
    Route::post('/admin/users/{user}/reset-password', [AdminController::class, 'userResetPassword'])->name('admin.users.reset-password');
    Route::post('/admin/users/{user}/delete', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
});

require __DIR__.'/settings.php';
