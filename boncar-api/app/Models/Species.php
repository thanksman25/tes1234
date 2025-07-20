<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scientific_name',
        'inaturalist_id',
        'description',
        'family'
    ];

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }

    public static function createOrUpdateFromInaturalist(array $data): self
    {
        // --- PERUBAHAN LOGIKA DI SINI ---
        // Gunakan updateOrCreate untuk mencegah duplikasi berdasarkan scientific_name
        return static::updateOrCreate(
            // Kunci untuk mencari: nama ilmiah adalah pengenal yang paling unik
            ['scientific_name' => $data['scientific_name']],
            // Data untuk diisi jika tidak ada, atau untuk diperbarui jika sudah ada
            [
                'name' => $data['name'],
                'inaturalist_id' => $data['inaturalist_id'],
                'description' => $data['description'] ?? null,
                'family' => $data['family'] ?? null
            ]
        );
    }
}