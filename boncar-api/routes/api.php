<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FormulaController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\SpeciesController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\DashboardController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Rute Publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// Grup Rute Terotentikasi
Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'auth:sanctum',
])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::middleware('verified')->group(function() {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        // ... dan semua rute Anda yang lain ...
        Route::get('/dashboard/user-stats', [DashboardController::class, 'getUserStats']);
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/dashboard/admin-stats', [DashboardController::class, 'getAdminStats']);
            Route::get('/submissions', [FormulaController::class, 'getSubmissions']);
        });
    });
});