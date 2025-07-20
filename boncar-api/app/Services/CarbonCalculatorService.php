<?php
namespace App\Services;

use App\Models\CalculationProject;
use App\Models\Setting;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CarbonCalculatorService
{
    private $expressionLanguage;
    private $settings;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->settings = Cache::rememberForever('app-settings', function () {
            return Setting::all()->pluck('value', 'key');
        });
    }

    public function calculate(CalculationProject $project)
    {
        $totalBiomassKg = 0;
        $trees = $project->trees()->with(['allometricEquation', 'species'])->get();

        foreach ($trees as $tree) {
            if (!$tree->allometricEquation) continue;
            
            $circumference = $tree->parameters['circumference'] ?? 0;
            $diameter = ($circumference > 0) ? $circumference / M_PI : 0;
            $formulaVars = [
                'K' => $circumference, 'D' => $diameter, 'H' => $tree->parameters['height'] ?? 0,
                'p' => $tree->parameters['wood_density'] ?? 0, 'AGB' => 0, 'BGB' => 0,
            ];
            
            try {
                // --- PERBAIKI SINTAKS RUMUS DI SINI ---
                $agbFormula = str_replace('^', '**', $tree->allometricEquation->formula_agb ?: '0');
                $formulaVars['AGB'] = $this->expressionLanguage->evaluate($agbFormula, $formulaVars);
                
                $bgbFormula = str_replace('^', '**', $tree->allometricEquation->formula_bgb ?: '0');
                $formulaVars['BGB'] = $this->expressionLanguage->evaluate($bgbFormula, $formulaVars);

            } catch (\Exception $e) {
                Log::error("Calculation Error for tree ID {$tree->id}: ".$e->getMessage());
                continue;
            }
            
            $currentParams = $tree->parameters;
            $currentParams['diameter'] = $diameter;
            $tree->parameters = $currentParams;
            $tree->biomass_agb_kg = $formulaVars['AGB'];

            $totalBiomassKg += ($formulaVars['AGB'] + $formulaVars['BGB']);
        }
        
        $landArea = $project->land_area > 0 ? $project->land_area : 1;
        
        if ($project->method === 'sampling' && $project->sample_area > 0) {
            $densityFactor = $project->land_area / $project->sample_area;
            $finalTotalBiomassKg = $totalBiomassKg * $densityFactor;
        } else {
            $finalTotalBiomassKg = $totalBiomassKg;
        }
        
        $carbonConversionFactor = (float) $this->settings->get('carbon_conversion_factor', 0.47);
        $finalTotalCarbonKg = $finalTotalBiomassKg * $carbonConversionFactor;

        $finalTotalCarbonTon = $finalTotalCarbonKg / 1000;
        $finalTotalBiomassTon = $finalTotalBiomassKg / 1000;
        
        $project->update(['total_carbon_stock' => $finalTotalCarbonTon]);
        
        return [
            'total_carbon_stock_ton' => $finalTotalCarbonTon,
            'total_biomass_ton' => $finalTotalBiomassTon,
            'carbon_per_ha_ton' => $finalTotalCarbonTon / $landArea,
            'biomass_per_ha_ton' => $finalTotalBiomassTon / $landArea,
            'trees' => $trees,
        ];
    }
}