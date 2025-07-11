<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationProject extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allometricEquation()
    {
        return $this->belongsTo(AllometricEquation::class);
    }

    public function trees()
    {
        return $this->hasMany(Tree::class);
    }
}