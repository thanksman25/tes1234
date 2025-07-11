<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpeciesController extends Controller
{
    /**
     * Mengambil daftar spesies dari database LOKAL.
     */
    public function index()
    {
        return Species::orderBy('name')->get();
    }

    /**
     * Mencari spesies secara real-time.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([]);
        }

        // 1. Cari dulu di database lokal kita untuk kecepatan
        $localResults = Species::where('name', 'like', "%{$query}%")
            ->orWhere('scientific_name', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        // Jika di lokal sudah ditemukan, langsung kembalikan hasilnya
        if ($localResults->isNotEmpty()) {
            return response()->json($localResults);
        }

        // 2. Jika tidak ada di lokal, baru cari ke API iNaturalist sebagai fallback
        $response = Http::get('https://api.inaturalist.org/v1/taxa/autocomplete', [
            'q' => $query,
            'is_active' => 'true',
            'taxon_id' => 47126,
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to search from external API.'], 500);
        }
        
        // Format hasil dari iNaturalist agar seragam dengan data lokal kita
        $formattedResults = collect($response->json()['results'])->map(function ($item) {
            return [
                'id' => null, // Tandakan ini bukan dari DB lokal
                'name' => $item['preferred_common_name'] ?? $item['name'],
                'scientific_name' => $item['name'],
                'inaturalist_id' => $item['id'],
            ];
        });

        return response()->json($formattedResults);
    }
}