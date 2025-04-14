<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



Route::prefix('users')->group(function () {
    // Mendapatkan semua user
    Route::get('/', [UserController::class, 'getUsers']);

    // Mendapatkan grup berdasarkan userId
    Route::get('{userId}/groups', [UserController::class, 'getGroups']);

    // Mendapatkan proyek berdasarkan userId
    Route::get('{userId}/projects', [UserController::class, 'getProjectsByUser']);

    // Mendapatkan proyek berdasarkan groupId
    Route::get('{userId}/groups/{groupId}/projects', [UserController::class, 'getProjectsByGroup']);

    // Mendapatkan tugas berdasarkan projectId
    Route::get('{userId}/groups/{groupId}/projects/{projectId}/tasks', [UserController::class, 'getTasks']);

    // Membuat user baru
    Route::post('/', [UserController::class, 'createUser']);

    // Menghapus user berdasarkan ID
    Route::delete('{userId}', [UserController::class, 'deleteUser']);

    // Menambahkan group ke user berdasarkan userId
    Route::post('{userId}/groups', [UserController::class, 'addGroupToUser']);

    // Menambahkan project ke group
    Route::post('{userId}/groups/{groupId}/projects', [UserController::class, 'addProjectToGroup']);

    // Menambahkan task ke project
    Route::post('{userId}/groups/{groupId}/projects/{projectId}/tasks', [UserController::class, 'addTaskToProject']);

    // Menghapus group
    Route::delete('{userId}/groups/{groupId}', [UserController::class, 'deleteGroup']);

    // Menghapus project
    Route::delete('{userId}/groups/{groupId}/projects/{projectId}', [UserController::class, 'deleteProject']);

    // Menghapus task
    Route::delete('{userId}/groups/{groupId}/projects/{projectId}/tasks/{taskId}', [UserController::class, 'deleteTask']);

    // Update task
    Route::put('{userId}/groups/{groupId}/projects/{projectId}/tasks/{taskId}', [UserController::class, 'updateTask']);

    // Update project
    Route::put('{userId}/groups/{groupId}/projects/{projectId}', [UserController::class, 'updateProject']);
});

