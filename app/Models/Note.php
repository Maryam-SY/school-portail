<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $guarded = [];
// Relations

public function eleve()
{
    return $this->belongsTo(\App\Models\Eleve::class);
}

public function matiere()
{
    return $this->belongsTo(\App\Models\Matiere::class);
}

public function enseignant()
{
    return $this->belongsTo(\App\Models\Enseignant::class);
}

}
