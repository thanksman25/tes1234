<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FormulaController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\SpeciesController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\DashboardController; // <-- TAMBAHKAN IMPORT INI
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Rute Publik (tidak memerlukan otentikasi)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// GRUP RUTE OTENTIKASI (STATEFUL API)
Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'auth:sanctum',
])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // RUTE YANG MEMBUTUHKAN EMAIL TERVERIFIKASI
    Route::middleware('verified')->group(function() {
        
        // Profil Pengguna
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // Formula Alometrik
        Route::get('/formulas', [FormulaController::class, 'index']);
        Route::post('/formulas/submit', [FormulaController::class, 'submit']);

        // Kalkulator
        Route::apiResource('/projects', CalculatorController::class);
        Route::post('/projects/{project}/trees', [CalculatorController::class, 'addTree']);
        Route::post('/projects/{project}/calculate', [CalculatorController::class, 'calculate']);

        // Data (contoh: spesies)
        Route::get('/species/search', [SpeciesController::class, 'search']);
        Route::get('/species', [SpeciesController::class, 'index']);

        // === RUTE BARU UNTUK DASHBOARD ===
        Route::get('/dashboard/user-stats', [DashboardController::class, 'getUserStats']);
        // ==================================

        // === RUTE KHUSUS ADMIN ===
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/submissions', [FormulaController::class, 'getSubmissions']);
            Route::post('/submissions/{submission}/approve', [FormulaController::class, 'approve']);
            Route::post('/submissions/{submission}/reject', [FormulaController::class, 'reject']);
            
            // === RUTE BARU UNTUK DASHBOARD ADMIN ===
            Route::get('/dashboard/admin-stats', [DashboardController::class, 'getAdminStats']);
            // =======================================
        });
    });
});