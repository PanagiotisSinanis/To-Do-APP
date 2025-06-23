<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\TaskManager;

// Login route
Route::post('/login', [AuthManager::class, 'apiLogin']);
Route::post('/register', [AuthManager::class, 'apiRegister']);

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
