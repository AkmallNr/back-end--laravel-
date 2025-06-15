<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/download', [HomeController::class, 'download'])->name('download');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Admin Routes (without authentication middleware for now)
//Route::prefix('admin')->group(function () {
    // User Management
Route::get('/users', [AdminController::class, 'listUser'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Group Management
    Route::get('/groups', [AdminController::class, 'listGroup'])->name('admin.groups');
    Route::get('/groups/{id}/edit', [AdminController::class, 'editGroup'])->name('admin.groups.edit');
    Route::put('/groups/{id}', [AdminController::class, 'updateGroup'])->name('admin.groups.update');
    Route::delete('/groups/{id}', [AdminController::class, 'deleteGroup'])->name('admin.groups.delete');

    // Task Management
    Route::get('/tasks', [AdminController::class, 'listTask'])->name('admin.tasks');
    Route::get('/tasks/{id}/edit', [AdminController::class, 'editTask'])->name('admin.tasks.edit');
    Route::put('/tasks/{id}', [AdminController::class, 'updateTask'])->name('admin.tasks.update');
    Route::delete('/tasks/{id}', [AdminController::class, 'deleteTask'])->name('admin.tasks.delete');

    // Schedule Management
    Route::get('/schedules', [AdminController::class, 'listSchedule'])->name('admin.schedules');
    Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('admin.schedules.edit');
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    Route::delete('/schedules/{id}', [AdminController::class, 'deleteSchedule'])->name('admin.schedules.delete');
//});
