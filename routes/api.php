<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\TaskManager;

// Login route
Route::post('/login', [AuthManager::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthManager::class, 'apiLogout']);
    Route::get('/tasks', [TaskManager::class, 'index']);
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API Routes working']);
});
