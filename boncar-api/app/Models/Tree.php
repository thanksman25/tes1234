<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
        ];
    }

    public function calculationProject()
    {
        return $this->belongsTo(CalculationProject::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}