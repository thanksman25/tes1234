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
use App\Http\Controllers\Api\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tidak Memerlukan Login)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::get('/species/search', [SpeciesController::class, 'search']);


/*
|--------------------------------------------------------------------------
| Rute Terotentikasi (Memerlukan Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |----------------------------------------------------------------------
    | Rute yang Memerlukan Verifikasi Email
    |----------------------------------------------------------------------
    */
    Route::middleware('verified')->group(function () {

        // --- Profil Pengguna ---
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // --- Dashboard ---
        Route::get('/dashboard/user-stats', [DashboardController::class, 'getUserStats']);

        // --- Spesies ---
        Route::get('/species', [SpeciesController::class, 'index']);
        // PENAMBAHAN ROUTE BARU UNTUK MENYIMPAN SPESIES DARI INATURALIST
        Route::post('/species/from-inaturalist', [SpeciesController::class, 'storeFromInaturalist']);
        
        // --- Rumus Alometrik (Akses Pengguna) ---
        Route::get('/formulas', [FormulaController::class, 'index']);
        Route::post('/formulas/submit', [FormulaController::class, 'submit']);

        // --- Proyek Kalkulator ---
        Route::apiResource('calculator-projects', CalculatorController::class)->parameters([
            'calculator-projects' => 'project'
        ]);
        
        /*
        |------------------------------------------------------------------
        | Rute Khusus Admin
        |------------------------------------------------------------------
        */
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/dashboard/admin-stats', [DashboardController::class, 'getAdminStats']);
            
            // Manajemen Pengajuan Rumus
            Route::get('/formula-submissions', [FormulaController::class, 'getSubmissions']);
            Route::post('/formula-submissions/{submission}/approve', [FormulaController::class, 'approve']);
            Route::post('/formula-submissions/{submission}/reject', [FormulaController::class, 'reject']);

            // Manajemen Rumus Aktif (CRUD LENGKAP)
            Route::post('/formulas', [FormulaController::class, 'store']); // CREATE
            Route::get('/formulas/{equation}', [FormulaController::class, 'show']); // READ ONE
            Route::put('/formulas/{equation}', [FormulaController::class, 'update']); // UPDATE
            Route::delete('/formulas/{equation}', [FormulaController::class, 'destroy']); // DELETE

            // Manajemen Pengguna
            Route::apiResource('users', UserController::class)->except(['store']);
            // MANAJEMEN PENGATURAN GLOBAL (BARU)
            Route::get('/settings', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'index']);
            Route::put('/settings', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'update']);

        });
    });
});