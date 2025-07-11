<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllometricEquation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function submission()
    {
        return $this->belongsTo(FormulaSubmission::class, 'submission_id');
    }
}