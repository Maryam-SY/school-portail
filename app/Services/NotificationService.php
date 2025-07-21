<?php

namespace App\Services;

use App\Models\Eleve;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Préparer les données de notification de bulletin
     * 
     * @param Eleve $eleve L'élève concerné
     * @param array $bulletin Le bulletin généré
     * @return array Données de notification
     */
    public function preparerNotificationBulletin(Eleve $eleve, array $bulletin)
    {
        return [
            'eleve_id' => $eleve->id,
            'eleve_nom' => $eleve->prenom . ' ' . $eleve->nom,
            'email' => $eleve->email,
            'periode' => $bulletin['periode'],
            'date_generation' => now()->format('Y-m-d H:i:s'),
            'moyenne_generale' => $bulletin['moyenne'],
            'statut' => 'disponible'
        ];
    }

    /**
     * Préparer les données d'identifiants de connexion
     * 
     * @param Eleve $eleve L'élève concerné
     * @param string $identifiant Identifiant de connexion
     * @param string $motDePasse Mot de passe temporaire
     * @return array Données d'identifiants
     */
    public function preparerIdentifiantsConnexion(Eleve $eleve, string $identifiant, string $motDePasse)
    {
        return [
            'eleve_id' => $eleve->id,
            'eleve_nom' => $eleve->prenom . ' ' . $eleve->nom,
            'email' => $eleve->email,
            'identifiant' => $identifiant,
            'date_generation' => now()->format('Y-m-d H:i:s')
        ];
    }
} 