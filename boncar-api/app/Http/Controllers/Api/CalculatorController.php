<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalculationProject;
use App\Services\CarbonCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    /**
     * Menyimpan proyek kalkulasi baru beserta semua data pohonnya.
     * Metode ini diubah total untuk menerima semua data sekaligus.
     */
    public function store(Request $request, CarbonCalculatorService $calculator)
    {
        $validatedData = $request->validate([
            // Validasi untuk detail proyek
            'project_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'land_area' => 'required|numeric|min:0',
            'method' => 'required|in:census,sampling',
            
            // Validasi untuk data pohon (harus berupa array)
            'trees' => 'required|array|min:1',
            'trees.*.species_id' => 'required|exists:species,id',
            'trees.*.allometric_equation_id' => 'required|exists:allometric_equations,id',
            'trees.*.parameters' => 'required|array',
            // --- PERUBAHAN VALIDASI DI SINI ---
            'trees.*.parameters.circumference' => 'required|numeric|min:0',
            // ---------------------------------
            'trees.*.parameters.height' => 'nullable|numeric|min:0',
            'trees.*.parameters.wood_density' => 'nullable|numeric|min:0',
        ]);

        try {
            // Gunakan transaction untuk memastikan semua data berhasil disimpan
            $project = DB::transaction(function () use ($validatedData) {
                // 1. Buat Proyek Kalkulasi
                $project = Auth::user()->calculationProjects()->create([
                    'project_name' => $validatedData['project_name'],
                    'province' => $validatedData['province'],
                    'city' => $validatedData['city'],
                    'district' => $validatedData['district'],
                    'village' => $validatedData['village'],
                    'land_area' => $validatedData['land_area'],
                    'method' => $validatedData['method'],
                    // Kita ambil ID rumus pertama sebagai rumus utama proyek (untuk rekap)
                    'allometric_equation_id' => $validatedData['trees'][0]['allometric_equation_id'],
                ]);

                // 2. Simpan setiap pohon yang berelasi dengan proyek
                foreach ($validatedData['trees'] as $treeData) {
                    $project->trees()->create([
                        'species_id' => $treeData['species_id'],
                        'allometric_equation_id' => $treeData['allometric_equation_id'],
                        'parameters' => $treeData['parameters'],
                    ]);
                }
                
                return $project;
            });
            
            // 3. Lakukan kalkulasi setelah semua data tersimpan
            $totalCarbon = $calculator->calculate($project->fresh());

            // Muat relasi yang dibutuhkan untuk response
            $project->load('trees.species', 'trees.allometricEquation');

            return response()->json([
                'message' => 'Project created and calculation successful.',
                'total_carbon_stock_ton' => $totalCarbon,
                'project' => $project,
            ], 201);

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan response error
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

    public function addTree(Request $request, CalculationProject $project)
    {
        $this->authorize('update', $project);

        $validatedData = $request->validate([
            'species_id' => 'required|exists:species,id',
            'allometric_equation_id' => 'required|exists:allometric_equations,id',
            'parameters' => 'required|array',
            'parameters.circumference' => 'required|numeric|min:0',
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
            'project' => $project->fresh()->load('trees.species', 'trees.allometricEquation')
        ]);
    }

    public function destroy(CalculationProject $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }
}