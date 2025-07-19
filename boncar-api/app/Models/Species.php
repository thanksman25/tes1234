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
        return static::firstOrCreate(
            ['inaturalist_id' => $data['inaturalist_id']],
            [
                'name' => $data['name'],
                'scientific_name' => $data['scientific_name'],
                'description' => $data['description'] ?? null,
                'family' => $data['family'] ?? null
            ]
        );
    }
}