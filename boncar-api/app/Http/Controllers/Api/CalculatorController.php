<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalculationProjectRequest;
use App\Models\CalculationProject;
use App\Models\Setting;
use App\Services\CarbonCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- TAMBAHKAN BARIS INI

class CalculatorController extends Controller
{
    use AuthorizesRequests; // <-- DAN TAMBAHKAN BARIS INI

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
            
            $settings = Cache::get('app-settings', Setting::all()->pluck('value', 'key'));
            
            $finalResultPayload = [
                'project' => $project,
                'trees' => $calculationResult['trees'],
                'total_biomass_ton' => $calculationResult['total_biomass_ton'],
                'total_carbon_ton' => $calculationResult['total_carbon_stock_ton'],
                'biomass_per_ha_ton' => $calculationResult['biomass_per_ha_ton'],
                'carbon_per_ha_ton' => $calculationResult['carbon_per_ha_ton'],
                'settings' => $settings,
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
    
    public function show(CalculationProject $project, CarbonCalculatorService $calculator)
    {
        $this->authorize('view', $project);

        $calculationResult = $calculator->calculate($project);
        
        $finalResultPayload = [
            'project' => $project,
            'trees' => $calculationResult['trees'],
            'total_biomass_ton' => $calculationResult['total_biomass_ton'],
            'total_carbon_ton' => $calculationResult['total_carbon_stock_ton'],
            'biomass_per_ha_ton' => $calculationResult['biomass_per_ha_ton'],
            'carbon_per_ha_ton' => $calculationResult['carbon_per_ha_ton'],
            'settings' => Cache::get('app-settings', Setting::all()->pluck('value', 'key')),
        ];

        return response()->json($finalResultPayload);
    }
    
    public function destroy(CalculationProject $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }
}