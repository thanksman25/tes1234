<?php

namespace App\Services;

use App\Models\CalculationProject;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CarbonCalculatorService
{
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function calculate(CalculationProject $project)
    {
        $equation = $project->allometricEquation->equation_template;
        $totalBiomass = 0;

        foreach ($project->trees as $tree) {
            // Variabel yang bisa digunakan di rumus: D (diameter), H (tinggi), p (kerapatan kayu)
            $formulaVars = [
                'D' => $tree->parameters['diameter'] ?? 0,
                'H' => $tree->parameters['height'] ?? 0,
                'p' => $tree->parameters['wood_density'] ?? 0,
            ];

            $biomassPerTree = $this->expressionLanguage->evaluate($equation, $formulaVars);
            $totalBiomass += $biomassPerTree;
        }

        // Standar IPCC, biomassa bawah tanah (BGB) adalah 26% dari atas tanah (AGB) untuk hutan tropis
        $totalBiomassWithBGB = $totalBiomass * 1.26;

        // Faktor konversi dari total biomassa ke karbon (standar IPCC 0.47)
        $carbonStockInKg = $totalBiomassWithBGB * 0.47;

        // Konversi dari Kg ke Ton
        $carbonStockInTon = $carbonStockInKg / 1000;

        $project->update(['total_carbon_stock' => $carbonStockInTon]);

        return $carbonStockInTon;
    }
}