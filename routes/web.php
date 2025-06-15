<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/download', [HomeController::class, 'download'])->name('download');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', [AdminController::class, 'listUser'])->name('users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Group Management
    Route::get('/groups', [AdminController::class, 'listGroup'])->name('groups');
    Route::get('/groups/{id}/edit', [AdminController::class, 'editGroup'])->name('groups.edit');
    Route::put('/groups/{id}', [AdminController::class, 'updateGroup'])->name('groups.update');
    Route::delete('/groups/{id}', [AdminController::class, 'deleteGroup'])->name('groups.delete');

    // Task Management
    Route::get('/tasks', [AdminController::class, 'listTask'])->name('tasks');
    Route::get('/tasks/{id}/edit', [AdminController::class, 'editTask'])->name('tasks.edit');
    Route::put('/tasks/{id}', [AdminController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{id}', [AdminController::class, 'deleteTask'])->name('tasks.delete');

    // Schedule Management
    Route::get('/schedules', [AdminController::class, 'listSchedule'])->name('schedules');
    Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('schedules.edit');
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{id}', [AdminController::class, 'deleteSchedule'])->name('schedules.delete');
});
