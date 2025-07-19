<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpeciesController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        // 1. Cari di database lokal dulu
        $localResults = Species::where('name', 'like', "%{$request->q}%")
            ->orWhere('scientific_name', 'like', "%{$request->q}%")
            ->limit(5)
            ->get()
            ->map(function ($species) {
                return [
                    ...$species->toArray(),
                    'is_local' => true
                ];
            });

        if ($localResults->isNotEmpty()) {
            return $localResults;
        }

        // 2. Fallback ke iNaturalist API
        try {
            $response = Http::get('https://api.inaturalist.org/v1/taxa/autocomplete', [
                'q' => $request->q,
                'is_active' => 'true',
                'taxon_id' => 47126 // Filter untuk tanaman
            ]);

            return collect($response->json()['results'])->map(function ($item) {
                return [
                    'id' => null,
                    'name' => $item['preferred_common_name'] ?? $item['name'],
                    'scientific_name' => $item['name'],
                    'inaturalist_id' => $item['id'],
                    'description' => $item['wikipedia_summary'] ?? null,
                    'family' => $item['ancestors'][1]['name'] ?? null,
                    'is_local' => false
                ];
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to search species'], 500);
        }
    }

    public function storeFromInaturalist(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'inaturalist_id' => 'required|integer',
            'description' => 'nullable|string',
            'family' => 'nullable|string|max:255'
        ]);

        try {
            $species = Species::createOrUpdateFromInaturalist($validated);
            return response()->json($species, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save species',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}