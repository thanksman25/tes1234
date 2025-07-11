<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function trees()
    {
        return $this->hasMany(Tree::class);
    }
}