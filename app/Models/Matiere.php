<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relations
    //acces a la table classe
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere')
                    ->withPivot('classe_id');
    }
// une matiere a plusieurs notes
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
