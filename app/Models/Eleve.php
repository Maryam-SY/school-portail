<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $guarded = [];

   // Relations

public function classe()
{
    // Chaque élève appartient à UNE seule classe 
    return $this->belongsTo(Classe::class);
}

public function notes()
{
    // Un élève a plusieurs notes 
    return $this->hasMany(Note::class);
}

public function parentUser()
{
    return $this->belongsTo(User::class, 'parent_user_id');
}

}
