<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // HasApiTokens : Permet à l'utilisateur d'utiliser des tokens API 
    // Notifiable : Permet d’envoyer des notifications 
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


public function eleve()
{

    return $this->hasOne(Eleve::class, 'email', 'email');
}

public function enseignant()
{
    // Un utilisateur peut aussi être lié à un enseignant si les emails correspondent
    return $this->hasOne(Enseignant::class, 'email', 'email');
}

// Vérification de rôle

public function hasRole($role)
{
    
    return $this->role === $role;
}

}
