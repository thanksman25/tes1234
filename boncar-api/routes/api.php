<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FormulaController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\SpeciesController;

// Rute Publik (tidak perlu login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute yang Membutuhkan Otentikasi (harus login & email terverifikasi)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Formula Alometrik
    Route::get('/formulas', [FormulaController::class, 'index']); // Ambil semua formula aktif
    Route::post('/formulas/submit', [FormulaController::class, 'submit']); // Ajukan formula baru

    // Kalkulator
    Route::apiResource('/projects', CalculatorController::class); // CRUD untuk Proyek Kalkulasi
    Route::post('/projects/{project}/trees', [CalculatorController::class, 'addTree']); // Tambah pohon ke proyek
    Route::post('/projects/{project}/calculate', [CalculatorController::class, 'calculate']); // Hitung total karbon

    // Data (contoh: spesies)
    Route::get('/species/search', [SpeciesController::class, 'search']); // <-- TAMBAHKAN BARIS INI
    Route::get('/species', [SpeciesController::class, 'index']);

    // === Rute Khusus Admin ===
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/submissions', [FormulaController::class, 'getSubmissions']); // Lihat semua pengajuan
        Route::post('/submissions/{submission}/approve', [FormulaController::class, 'approve']); // Setujui
        Route::post('/submissions/{submission}/reject', [FormulaController::class, 'reject']); // Tolak
    });
});