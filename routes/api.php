<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;

Route::prefix('users')->group(function () {
    // Mendapatkan semua user
    Route::get('/', [UserController::class, 'getUsers']);

    // Mendapatkan grup berdasarkan userId
    Route::get('{userId}/groups', [UserController::class, 'getGroups']);

    // Mendapatkan proyek berdasarkan userId
    Route::get('{userId}/projects', [UserController::class, 'getProjectsByUser']);

    Route::get('{userId}/tasks', [UserController::class, 'getTaskByUser']);

    // Mendapatkan proyek berdasarkan groupId
    Route::get('{userId}/groups/{groupId}/projects', [UserController::class, 'getProjectsByGroup']);

    Route::get('{userId}/tasks/weekly-completed', [UserController::class, 'getWeeklyCompletedTasks']);

    Route::get('{userId}/groups/{groupId}/projects/{projectId}/tasks/completed-today', [UserController::class, 'getCompletedTasksCountToday']);

    // Mendapatkan tugas berdasarkan projectId
    Route::get('{userId}/groups/{groupId}/projects/{projectId}/tasks', [UserController::class, 'getTasks']);

    // Mendapatkan tugas berdasarkan taskId
    Route::get('{userId}/groups/{groupId}/projects/{projectId}/tasks/{taskId}', [UserController::class, 'getTaskById']);

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

    // Get quote
    Route::get('{userId}/quotes', [UserController::class, 'getQuotes']);

    // Add quote
    Route::post('{userId}/quotes', [UserController::class, 'addQuote']);

    // Update quote
    Route::put('{userId}/quotes/{quoteId}', [UserController::class, 'updateQuote']);

    // Delete quote
    Route::delete('{userId}/quotes/{quoteId}', [UserController::class, 'deleteQuote']);

    // Get schedule
    Route::get('{userId}/schedules', [UserController::class, 'getSchedule']);

    // Add schedule
    Route::post('{userId}/schedules', [UserController::class, 'addSchedule']);

    // Update schedule
    Route::put('{userId}/schedules/{scheduleId}', [UserController::class, 'updateSchedule']);

    // Delete schedule
    Route::delete('{userId}/schedules/{scheduleId}', [UserController::class, 'deleteSchedule']);

    // Update profile picture
    Route::post('{userId}/profile-picture', [UserController::class, 'updateProfilePicture']);

    Route::put('{userId}/groups/{groupId}', [UserController::class, 'updateGroup']);

    Route::get('{userId}/groups/{groupId}/projects/{projectId}/getTaskbyProj'. [UserController::class, 'getTaskByProject']);
});

Route::post('/upload-file', [UserController::class, 'upload']);

// Pindahkan rute google-login ke luar grup users
Route::post('/google-login', [UserController::class, 'loginWithGoogle']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk register
Route::post('/register', [UserController::class, 'register']);

// Route untuk login
Route::post('/login', [UserController::class, 'login']);
