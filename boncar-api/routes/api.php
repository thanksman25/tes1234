<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FormulaController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\SpeciesController;
use App\Http\Controllers\Api\EmailVerificationController;

// --- RUTE PUBLIK ---
// Rute ini dapat diakses oleh siapa saja.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- RUTE VERIFIKASI EMAIL ---
// Rute ini harus memiliki middleware 'auth:sanctum' agar Laravel bisa
// mengidentifikasi pengguna dari URL yang ditandatangani (signed URL) saat diklik.
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth:sanctum', 'signed']) // PERUBAHAN KUNCI ADA DI SINI
    ->name('verification.verify'); // Nama ini wajib agar Laravel bisa membuat URL verifikasi.

// --- RUTE YANG MEMBUTUHKAN OTENTIKASI ---
// Semua rute di dalam grup ini hanya bisa diakses jika pengguna sudah login.
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute untuk mengirim ulang email verifikasi jika pengguna memintanya.
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1') // Batasi pengiriman ulang: 6 kali per menit.
        ->name('verification.send');

    // --- RUTE YANG MEMBUTUHKAN EMAIL TERVERIFIKASI ---
    // Semua rute di dalam grup ini hanya bisa diakses jika pengguna sudah login DAN emailnya terverifikasi.
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

        // === RUTE KHUSUS ADMIN ===
        // Rute ini hanya bisa diakses oleh pengguna dengan peran 'admin'.
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/submissions', [FormulaController::class, 'getSubmissions']);
            Route::post('/submissions/{submission}/approve', [FormulaController::class, 'approve']);
            Route::post('/submissions/{submission}/reject', [FormulaController::class, 'reject']);
        });
    });
});
