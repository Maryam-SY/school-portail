<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relations

public function matieres()
{
    // Un enseignant peut enseigner plusieurs matières
    return $this->belongsToMany(Matiere::class, 'enseignant_matiere')
                ->withPivot('classe_id');
}

public function classes()
{
    // Un enseignant peut enseigner dans plusieurs classes
    // On récupère aussi le "matiere_id" associé à chaque classe où il enseigne
    return $this->belongsToMany(Classe::class, 'enseignant_matiere')
                ->withPivot('matiere_id');
}

}
