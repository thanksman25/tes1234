<?php

namespace App\Console\Commands;

use App\Models\Species;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchSpeciesData extends Command
{
    protected $signature = 'species:fetch';
    protected $description = 'Fetch popular species data from iNaturalist observations in Indonesia.';

    public function handle()
    {
        $this->info('Fetching popular species observations from iNaturalist (Indonesia)...');

        $response = Http::get('https://api.inaturalist.org/v1/observations', [
            'taxon_id' => 47126,
            'place_id' => 6926,
            'popular' => 'true',
            'verifiable' => 'true',
            'locale' => 'id',
            'per_page' => 200,
            'order_by' => 'observations_count'
        ]);

        if ($response->failed()) {
            $this->error('Failed to fetch data from iNaturalist API.');
            Log::error('iNaturalist API fetch failed: ' . $response->body());
            return 1;
        }

        $observations = $response->json()['results'];
        $uniqueSpecies = collect($observations)
            ->pluck('taxon')
            ->filter()
            ->unique('id');

        if ($uniqueSpecies->isEmpty()) {
            $this->warn('No species found in the API response. Please check the API parameters.');
            return;
        }

        $this->withProgressBar($uniqueSpecies, function ($species) {
            if (isset($species['rank_level']) && $species['rank_level'] <= 10) {
                Species::updateOrCreate(
                    ['inaturalist_id' => $species['id']],
                    [
                        'name' => $species['preferred_common_name'] ?? $species['name'],
                        'scientific_name' => $species['name'],
                    ]
                );
            }
        });

        $this->newLine();
        $this->info('Successfully fetched and stored species data.');
        return 0;
    }
}