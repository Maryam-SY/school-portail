<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    

    public function eleves()
    {
        // Une classe a plusieurs élèves
        return $this->hasMany(Eleve::class);
    }

    public function enseignants()
    {
        // Une classe est liée à plusieurs enseignants via une table pivot "enseignant_matiere"
        // La table pivot contient aussi la colonne 'matiere_id' qu'on veut récupérer

        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere')
                    ->withPivot('matiere_id'); 
    }
}
