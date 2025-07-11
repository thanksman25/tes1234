<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FormulaController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\SpeciesController;
use App\Http\Controllers\Api\EmailVerificationController; // <-- IMPORT BARU

// --- RUTE PUBLIK ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTE VERIFIKASI EMAIL (Sangat Penting) ---
// Rute ini harus bisa diakses bahkan oleh pengguna yang belum login (saat mereka mengklik link email)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed']) // Memvalidasi signature pada URL
    ->name('verification.verify'); // WAJIB: Memberi nama rute yang dicari Laravel

// --- RUTE YANG MEMBUTUHKAN OTENTIKASI ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute untuk mengirim ulang email verifikasi
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1') // Batasi pengiriman ulang
        ->name('verification.send');

    // Rute yang hanya bisa diakses setelah email terverifikasi
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

        // === Rute Khusus Admin ===
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/submissions', [FormulaController::class, 'getSubmissions']);
            Route::post('/submissions/{submission}/approve', [FormulaController::class, 'approve']);
            Route::post('/submissions/{submission}/reject', [FormulaController::class, 'reject']);
        });
    });
});
