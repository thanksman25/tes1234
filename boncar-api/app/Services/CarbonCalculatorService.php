<?php

namespace App\Services;

use App\Models\CalculationProject;
use App\Models\Tree;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Illuminate\Support\Facades\Log;

class CarbonCalculatorService
{
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function calculate(CalculationProject $project)
    {
        $totalCarbonStockKg = 0;

        // Muat semua pohon beserta relasi rumusnya untuk efisiensi
        $trees = $project->trees()->with('allometricEquation')->get();

        foreach ($trees as $tree) {
            if (!$tree->allometricEquation) {
                Log::warning("Pohon ID {$tree->id} tidak memiliki relasi rumus, dilewati.");
                continue;
            }
            
            // Ambil keliling dari parameter
            $keliling = $tree->parameters['circumference'] ?? 0;

            // Variabel dasar dari data pohon
            $formulaVars = [
                'K' => $keliling,
                // Hitung Diameter secara otomatis jika Keliling ada
                'D' => ($keliling > 0) ? $keliling / M_PI : 0,
                'H' => $tree->parameters['height'] ?? 0,
                'p' => $tree->parameters['wood_density'] ?? 0,
                'AGB' => 0, // Inisialisasi
                'BGB' => 0, // Inisialisasi
            ];

            // 1. Hitung AGB (Above Ground Biomass)
            $agbFormula = $tree->allometricEquation->formula_agb ?: $tree->allometricEquation->equation_template;
            if ($agbFormula) {
                try {
                    $formulaVars['AGB'] = $this->expressionLanguage->evaluate($agbFormula, $formulaVars);
                } catch (\Exception $e) {
                    Log::error("Error evaluasi AGB untuk pohon ID {$tree->id}: ".$e->getMessage());
                    continue; // Lewati pohon ini jika rumus error
                }
            }

            // 2. Hitung BGB (Below Ground Biomass)
            $bgbFormula = $tree->allometricEquation->formula_bgb;
            if ($bgbFormula) {
                 try {
                    $formulaVars['BGB'] = $this->expressionLanguage->evaluate($bgbFormula, $formulaVars);
                } catch (\Exception $e) {
                    Log::error("Error evaluasi BGB untuk pohon ID {$tree->id}: ".$e->getMessage());
                    continue;
                }
            }

            // 3. Hitung Total Karbon untuk pohon ini
            $carbonFormula = $tree->allometricEquation->formula_carbon;
            $carbonPerTreeKg = 0;
            if ($carbonFormula) {
                try {
                    $carbonPerTreeKg = $this->expressionLanguage->evaluate($carbonFormula, $formulaVars);
                } catch (\Exception $e) {
                    Log::error("Error evaluasi Karbon untuk pohon ID {$tree->id}: ".$e->getMessage());
                    continue;
                }
            }
            
            $totalCarbonStockKg += $carbonPerTreeKg;
        }

        // Konversi dari Kg ke Ton
        $totalCarbonStockTon = $totalCarbonStockKg / 1000;
        
        // Simpan hasil akhir ke proyek
        $project->update(['total_carbon_stock' => $totalCarbonStockTon]);

        return $totalCarbonStockTon;
    }
}