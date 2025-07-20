<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalculationProjectRequest;
use App\Models\CalculationProject;
use App\Services\CarbonCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculatorController extends Controller
{
    public function index()
    {
        $projects = Auth::user()
            ->calculationProjects()
            ->withCount('trees')
            ->latest()
            ->get();
            
        return response()->json($projects);
    }

    public function store(StoreCalculationProjectRequest $request, CarbonCalculatorService $calculator)
    {
        $validatedData = $request->validated();

        try {
            $project = DB::transaction(function () use ($validatedData) {
                $projectData = Arr::except($validatedData, ['trees']);
                $projectData['allometric_equation_id'] = $validatedData['trees'][0]['allometric_equation_id'];
                $newProject = Auth::user()->calculationProjects()->create($projectData);

                foreach ($validatedData['trees'] as $treeData) {
                    $newProject->trees()->create($treeData);
                }
                
                return $newProject;
            });
            
            $calculationResult = $calculator->calculate($project->fresh());
            
            // --- GABUNGKAN SEMUA DATA HASIL KALKULASI KE DALAM SATU OBJEK ---
            $finalResultPayload = [
                'project' => $project,
                'trees' => $calculationResult['trees'],
                'total_biomass_ton' => $calculationResult['total_biomass_ton'],
                'total_carbon_ton' => $calculationResult['total_carbon_stock_ton'],
                'biomass_per_ha_ton' => $calculationResult['biomass_per_ha_ton'],
                'carbon_per_ha_ton' => $calculationResult['carbon_per_ha_ton'],
            ];

            return response()->json($finalResultPayload, 201);

        } catch (\Exception $e) {
            Log::error('Error during calculation project creation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create project. An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(CalculationProject $project)
    {
        $this->authorize('view', $project);
        $project->load('trees.species', 'trees.allometricEquation', 'allometricEquation');
        return response()->json($project);
    }
    
    public function destroy(CalculationProject $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }
}