<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllometricEquation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'equation_template', // Tetap ada untuk kompatibilitas atau catatan
        'reference',
        'submission_id',
        // Kolom-kolom baru untuk rumus dinamis
        'formula_agb',
        'formula_bgb',
        'formula_carbon',
        'required_parameters',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Pastikan kolom ini selalu di-handle sebagai array/json
            'required_parameters' => 'array',
        ];
    }

    public function submission()
    {
        return $this->belongsTo(FormulaSubmission::class, 'submission_id');
    }
}