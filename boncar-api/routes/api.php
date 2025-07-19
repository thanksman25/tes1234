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
// --- TAMBAHKAN USE STATEMENT INI ---
use App\Http\Controllers\Api\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tidak Memerlukan Login)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute untuk verifikasi email (pengguna mengklik link dari email)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// Rute untuk mencari spesies, bisa diakses publik jika diperlukan
Route::get('/species/search', [SpeciesController::class, 'search']);


/*
|--------------------------------------------------------------------------
| Rute Terotentikasi (Memerlukan Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Mengambil data pengguna yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Mengirim ulang email verifikasi
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Logout
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
        
        // --- Rumus Alometrik ---
        Route::get('/formulas', [FormulaController::class, 'index']);
        Route::post('/formulas/submit', [FormulaController::class, 'submit']);

        // --- Proyek Kalkulator ---
        Route::apiResource('calculator-projects', CalculatorController::class)->parameters([
            'calculator-projects' => 'project'
        ]);
        Route::post('/calculator-projects/{project}/trees', [CalculatorController::class, 'addTree']);
        Route::post('/calculator-projects/{project}/calculate', [CalculatorController::class, 'calculate']);
        
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

            // --- TAMBAHKAN BLOK KODE INI ---
            // Manajemen Pengguna
            Route::apiResource('users', UserController::class)->except(['store']);
            // ------------------------------------
        });
    });
});