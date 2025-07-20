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
    // Chaque note appartient à UN élève
    return $this->belongsTo(Eleve::class);
}

public function matiere()
{
    
    return $this->belongsTo(Matiere::class);
}

}
