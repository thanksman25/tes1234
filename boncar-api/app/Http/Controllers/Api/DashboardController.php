<?php
// boncar-api/app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AllometricEquation;
use App\Models\FormulaSubmission;
use App\Models\CalculationProject;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mengambil statistik untuk dashboard pengguna biasa.
     */
    public function getUserStats()
    {
        $user = Auth::user();

        // Menghitung jumlah proyek (lokasi) milik pengguna
        $locationsCount = $user->calculationProjects()->count();

        // Menghitung jumlah total pohon dari semua proyek milik pengguna
        $treesCount = DB::table('trees')
            ->whereIn('calculation_project_id', $user->calculationProjects()->pluck('id'))
            ->count();

        // Menghitung total cadangan karbon dari semua proyek milik pengguna
        $totalCarbon = $user->calculationProjects()->sum('total_carbon_stock');

        return response()->json([
            'locations' => $locationsCount,
            'trees' => $treesCount,
            'carbon_stock' => round($totalCarbon, 2) // Dibulatkan 2 desimal
        ]);
    }

    /**
     * Mengambil statistik untuk dashboard admin.
     * Diberi middleware 'admin' di file routes/api.php
     */
    public function getAdminStats()
    {
        $usersCount = User::count();
        $allometricEquationsCount = AllometricEquation::count();
        $pendingSubmissionsCount = FormulaSubmission::where('status', 'pending')->count();

        return response()->json([
            'users' => $usersCount,
            'formulas' => $allometricEquationsCount,
            'pending_submissions' => $pendingSubmissionsCount,
        ]);
    }
}