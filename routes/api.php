<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogsController as AdminLogsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'storeComment']);
    Route::get('priorities', [PriorityController::class, 'index']);
    Route::apiResource('labels', LabelController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/logs', [AdminLogsController::class, 'index']);
        Route::get('/tickets/{ticket}/logs', [TicketController::class, 'ticketLogs']);
        Route::apiResource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
