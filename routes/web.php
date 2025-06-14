<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/download', [HomeController::class, 'download'])->name('download');

// Admin Login and Logout Routes (no auth middleware)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('logout');
});

// Admin Protected Routes with auth:admin middleware
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/groups', [AdminController::class, 'listGroup'])->name('admin.groups');
    Route::get('/groups/{id}/edit', [AdminController::class, 'editGroup'])->name('admin.groups.edit');
    Route::put('/groups/{id}', [AdminController::class, 'updateGroup'])->name('admin.groups.update');
    Route::delete('/groups/{id}', [AdminController::class, 'deleteGroup'])->name('admin.groups.delete');

    Route::get('/tasks', [AdminController::class, 'listTask'])->name('admin.tasks');
    Route::get('/tasks/{id}/edit', [AdminController::class, 'editTask'])->name('admin.tasks.edit');
    Route::put('/tasks/{id}', [AdminController::class, 'updateTask'])->name('admin.tasks.update');
    Route::delete('/tasks/{id}', [AdminController::class, 'deleteTask'])->name('admin.tasks.delete');

    Route::get('/schedules', [AdminController::class, 'listSchedule'])->name('admin.schedules');
    Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('admin.schedules.edit');
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    Route::delete('/schedules/{id}', [AdminController::class, 'deleteSchedule'])->name('admin.schedules.delete');

    // Rute untuk admin.users
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit'); // Tambahkan rute edit
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete'); // Tambahkan rute delete
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
});