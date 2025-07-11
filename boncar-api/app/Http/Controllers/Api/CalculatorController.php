<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalculationProject;
use App\Services\CarbonCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalculatorController extends Controller
{
    /**
     * Menampilkan semua proyek kalkulasi milik pengguna.
     * DITAMBAHKAN: withCount('trees') untuk efisiensi.
     */
    public function index()
    {
        $projects = Auth::user()
            ->calculationProjects()
            ->withCount('trees') // <-- TAMBAHAN: Menghitung jumlah pohon terkait
            ->latest()
            ->get();
            
        return response()->json($projects);
    }

    // ... (sisa fungsi lainnya tidak perlu diubah) ...
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'land_area' => 'required|numeric|min:0',
            'method' => 'required|in:census,sampling',
            'allometric_equation_id' => 'required|exists:allometric_equations,id',
        ]);

        $project = Auth::user()->calculationProjects()->create($validatedData);

        return response()->json($project, 201);
    }

    public function show(CalculationProject $project)
    {
        $this->authorize('view', $project);

        $project->load('trees.species', 'allometricEquation');
        return response()->json($project);
    }

    public function addTree(Request $request, CalculationProject $project)
    {
        $this->authorize('update', $project);

        $validatedData = $request->validate([
            'species_id' => 'required|exists:species,id',
            'parameters' => 'required|array',
            'parameters.diameter' => 'required|numeric|min:0',
            'parameters.height' => 'nullable|numeric|min:0',
            'parameters.wood_density' => 'nullable|numeric|min:0',
        ]);

        $tree = $project->trees()->create($validatedData);

        return response()->json($tree, 201);
    }
    
    public function calculate(CalculationProject $project, CarbonCalculatorService $calculator)
    {
        $this->authorize('update', $project);
        
        $totalCarbon = $calculator->calculate($project);
        
        return response()->json([
            'message' => 'Calculation successful.',
            'total_carbon_stock_ton' => $totalCarbon,
            'project' => $project->fresh()
        ]);
    }

    public function destroy(CalculationProject $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }
}