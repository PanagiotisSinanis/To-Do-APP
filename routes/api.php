<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\TaskManager;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

// Login route

Route::post('/login', [AuthManager::class, 'apiLogin']);
Route::post('/register', [AuthManager::class, 'apiRegister']);

Route::middleware('auth:sanctum')->post('/projects', [ProjectController::class, 'store']);
Route::middleware('auth:sanctum')->get('/projects', [ProjectController::class, 'index']);

Route::middleware('auth:sanctum')->get('/my-projects', [ProjectController::class, 'userProjects']);

Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/projects', [ProjectController::class, 'index']);
Route::middleware('auth:sanctum')->get('/projects/{id}', [ProjectController::class, 'show']);
Route::middleware('auth:sanctum')->get('/projects/{id}', [ProjectController::class, 'show']);


Route::middleware(['auth:sanctum', 'role:superadmin'])
    ->get('/admin/dashboard', [AdminController::class, 'dashboard']);

Route::middleware(['auth:sanctum', 'role:user'])
    ->get('/user/dashboard', [UserController::class, 'dashboard']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthManager::class, 'apiLogout']);
     Route::delete('/tasks/{id}', [TaskManager::class, 'destroy']); // Delete a task
    Route::put('/tasks/{id}', [TaskManager::class, 'update']); // Update a task
     Route::post('/tasks', [TaskManager::class, 'store']); // Create a new task
    Route::get('/tasks', [TaskManager::class, 'index']);
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API Routes working']);
});
