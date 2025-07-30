# API Laravel pour la Gestion des Bulletins

## 📋 **Routes API à ajouter dans `routes/api.php`**

```php
<?php

use App\Http\Controllers\BulletinController;
use Illuminate\Support\Facades\Route;

// Routes pour la gestion des bulletins
Route::prefix('bulletins')->group(function () {
    // Récupérer tous les bulletins avec détails complets
    Route::get('/tous-avec-details', [BulletinController::class, 'getAllWithDetails']);
    
    // Récupérer les bulletins d'une classe pour une période
    Route::get('/classe/{classeId}/periode/{periode}', [BulletinController::class, 'getByClasseAndPeriode']);
    
    // Récupérer le bulletin d'un élève spécifique
    Route::get('/eleve/{eleveId}/periode/{periode}', [BulletinController::class, 'getByEleveAndPeriode']);
    
    // Télécharger un bulletin PDF
    Route::get('/pdf/{eleveId}/{periode}', [BulletinController::class, 'downloadPDF']);
    
    // Télécharger tous les bulletins d'une classe en ZIP
    Route::get('/zip/classe/{classeId}/periode/{periode}', [BulletinController::class, 'downloadZip']);
    
    // Calculer les moyennes et mentions pour une classe
    Route::post('/calculer/classe/{classeId}/periode/{periode}', [BulletinController::class, 'calculerBulletins']);
    
    // Statistiques des bulletins
    Route::get('/statistiques', [BulletinController::class, 'getStatistiques']);
});
```

## 🎯 **Contrôleur Laravel complet**

### `app/Http/Controllers/BulletinController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Note;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    /**
     * Récupérer tous les bulletins avec détails complets
     */
    public function getAllWithDetails(): JsonResponse
    {
        try {
            $eleves = Eleve::with(['classe', 'notes.matiere'])->get();
            $bulletins = [];

            foreach ($eleves as $eleve) {
                // Récupérer toutes les périodes de l'élève
                $periodes = $eleve->notes->pluck('periode')->unique();
                
                foreach ($periodes as $periode) {
                    $notes = $eleve->notes->where('periode', $periode);
                    
                    if ($notes->isNotEmpty()) {
                        // Calculer la moyenne
                        $moyenne = round($notes->avg('valeur'), 2);
                        
                        // Calculer la mention
                        $mention = $this->getMention($moyenne);
                        
                        // Calculer le rang
                        $rang = $this->calculerRang($eleve->id, $eleve->classe_id, $periode);
                        
                        $bulletins[] = [
                            'eleve' => [
                                'id' => $eleve->id,
                                'nom' => $eleve->nom,
                                'prenom' => $eleve->prenom,
                                'email' => $eleve->email
                            ],
                            'classe' => $eleve->classe->nom ?? 'Non assigné',
                            'periode' => $periode,
                            'moyenne' => $moyenne,
                            'mention' => $mention,
                            'rang' => $rang,
                            'nb_notes' => $notes->count(),
                            'notes' => $notes->map(function($note) {
                                return [
                                    'matiere' => $note->matiere->nom ?? 'Matière inconnue',
                                    'note' => $note->valeur,
                                    'type' => $note->type_evaluation ?? 'Non spécifié'
                                ];
                            })
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $bulletins,
                'total' => count($bulletins)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des bulletins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les bulletins d'une classe pour une période
     */
    public function getByClasseAndPeriode($classeId, $periode): JsonResponse
    {
        try {
            $classe = Classe::with(['eleves.notes.matiere'])->findOrFail($classeId);
            $eleves = $classe->eleves;
            $bulletins = [];

            foreach ($eleves as $eleve) {
                $notes = $eleve->notes->where('periode', $periode);
                
                if ($notes->isNotEmpty()) {
                    $moyenne = round($notes->avg('valeur'), 2);
                    $mention = $this->getMention($moyenne);
                    $rang = $this->calculerRang($eleve->id, $classeId, $periode);
                    
                    $bulletins[] = [
                        'eleve' => [
                            'id' => $eleve->id,
                            'nom' => $eleve->nom,
                            'prenom' => $eleve->prenom
                        ],
                        'classe' => $classe->nom,
                        'periode' => $periode,
                        'moyenne' => $moyenne,
                        'mention' => $mention,
                        'rang' => $rang,
                        'notes' => $notes->map(function($note) {
                            return [
                                'matiere' => $note->matiere->nom ?? 'Matière inconnue',
                                'note' => $note->valeur,
                                'type' => $note->type_evaluation ?? 'Non spécifié'
                            ];
                        })
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $bulletins,
                'classe' => $classe->nom,
                'periode' => $periode,
                'total' => count($bulletins)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des bulletins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le bulletin d'un élève spécifique
     */
    public function getByEleveAndPeriode($eleveId, $periode): JsonResponse
    {
        try {
            $eleve = Eleve::with(['classe', 'notes.matiere'])->findOrFail($eleveId);
            $notes = $eleve->notes->where('periode', $periode);
            
            if ($notes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune note trouvée pour cet élève et cette période'
                ], 404);
            }

            $moyenne = round($notes->avg('valeur'), 2);
            $mention = $this->getMention($moyenne);
            $rang = $this->calculerRang($eleve->id, $eleve->classe_id, $periode);

            $bulletin = [
                'eleve' => [
                    'id' => $eleve->id,
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom
                ],
                'classe' => $eleve->classe->nom ?? 'Non assigné',
                'periode' => $periode,
                'moyenne' => $moyenne,
                'mention' => $mention,
                'rang' => $rang,
                'notes' => $notes->map(function($note) {
                    return [
                        'matiere' => $note->matiere->nom ?? 'Matière inconnue',
                        'note' => $note->valeur,
                        'type' => $note->type_evaluation ?? 'Non spécifié'
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $bulletin
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du bulletin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger un bulletin PDF
     */
    public function downloadPDF($eleveId, $periode)
    {
        try {
            $eleve = Eleve::with(['classe', 'notes.matiere'])->findOrFail($eleveId);
            $notes = $eleve->notes->where('periode', $periode);
            
            if ($notes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune note trouvée pour cet élève et cette période'
                ], 404);
            }

            $moyenne = round($notes->avg('valeur'), 2);
            $mention = $this->getMention($moyenne);
            $rang = $this->calculerRang($eleve->id, $eleve->classe_id, $periode);

            $bulletin = [
                'eleve' => [
                    'nom' => $eleve->nom,
                    'prenom' => $eleve->prenom
                ],
                'classe' => $eleve->classe->nom ?? 'Non assigné',
                'periode' => $periode,
                'moyenne' => $moyenne,
                'mention' => $mention,
                'rang' => $rang,
                'notes' => $notes->map(function($note) {
                    return [
                        'matiere' => $note->matiere->nom ?? 'Matière inconnue',
                        'note' => $note->valeur,
                        'type' => $note->type_evaluation ?? 'Non spécifié'
                    ];
                })
            ];

            $pdf = Pdf::loadView('bulletins.pdf', compact('bulletin'));
            $filename = 'bulletin_' . $eleve->nom . '_' . $periode . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer les bulletins pour une classe
     */
    public function calculerBulletins($classeId, $periode): JsonResponse
    {
        try {
            $classe = Classe::with(['eleves.notes.matiere'])->findOrFail($classeId);
            $eleves = $classe->eleves;
            $bulletins = [];

            foreach ($eleves as $eleve) {
                $notes = $eleve->notes->where('periode', $periode);
                
                if ($notes->isNotEmpty()) {
                    $moyenne = round($notes->avg('valeur'), 2);
                    $mention = $this->getMention($moyenne);
                    $rang = $this->calculerRang($eleve->id, $classeId, $periode);
                    
                    $bulletins[] = [
                        'eleve' => [
                            'id' => $eleve->id,
                            'nom' => $eleve->nom,
                            'prenom' => $eleve->prenom
                        ],
                        'moyenne' => $moyenne,
                        'mention' => $mention,
                        'rang' => $rang
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $bulletins,
                'classe' => $classe->nom,
                'periode' => $periode,
                'total' => count($bulletins)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des bulletins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des bulletins
     */
    public function getStatistiques(): JsonResponse
    {
        try {
            $totalEleves = Eleve::count();
            $totalClasses = Classe::count();
            $totalNotes = Note::count();
            $moyenneGenerale = Note::avg('valeur');

            $statistiques = [
                'total_eleves' => $totalEleves,
                'total_classes' => $totalClasses,
                'total_notes' => $totalNotes,
                'moyenne_generale' => round($moyenneGenerale, 2),
                'periodes_disponibles' => Note::distinct('periode')->pluck('periode')
            ];

            return response()->json([
                'success' => true,
                'data' => $statistiques
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer le rang d'un élève dans sa classe
     */
    private function calculerRang($eleveId, $classeId, $periode): int
    {
        $elevesClasse = Eleve::where('classe_id', $classeId)->pluck('id');
        $moyennes = [];

        foreach ($elevesClasse as $id) {
            $notes = Note::where('eleve_id', $id)
                        ->where('periode', $periode)
                        ->get();

            if ($notes->isNotEmpty()) {
                $moyenne = round($notes->avg('valeur'), 2);
                $moyennes[$id] = $moyenne;
            }
        }

        // Trier par moyenne décroissante
        arsort($moyennes);

        $rang = 1;
        foreach ($moyennes as $id => $moyenne) {
            if ($id == $eleveId) {
                return $rang;
            }
            $rang++;
        }

        return count($moyennes);
    }

    /**
     * Déterminer la mention selon la moyenne
     */
    private function getMention($moyenne): string
    {
        if ($moyenne >= 16) return 'Très Bien';
        if ($moyenne >= 14) return 'Bien';
        if ($moyenne >= 12) return 'Assez Bien';
        if ($moyenne >= 10) return 'Passable';
        return 'Insuffisant';
    }
}
```

## 📊 **Modèles Laravel requis**

### Vérifier que ces modèles existent avec les bonnes relations :

#### `app/Models/Eleve.php`
```php
public function classe()
{
    return $this->belongsTo(Classe::class);
}

public function notes()
{
    return $this->hasMany(Note::class);
}
```

#### `app/Models/Classe.php`
```php
public function eleves()
{
    return $this->hasMany(Eleve::class);
}
```

#### `app/Models/Note.php`
```php
public function eleve()
{
    return $this->belongsTo(Eleve::class);
}

public function matiere()
{
    return $this->belongsTo(Matiere::class);
}
```

## 🔧 **Configuration CORS**

### Dans `config/cors.php` :
```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:4200'], // URL de ton app Angular
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## 📋 **Étapes d'implémentation**

1. **Ajoute les routes** dans `routes/api.php`
2. **Crée le contrôleur** `BulletinController.php`
3. **Vérifie les modèles** existent avec les bonnes relations
4. **Configure CORS** pour Angular
5. **Teste l'API** avec Postman ou navigateur

## 🧪 **Test de l'API**

Teste ces URLs dans ton navigateur ou Postman :

- `GET http://localhost:8000/api/bulletins/tous-avec-details`
- `GET http://localhost:8000/api/bulletins/statistiques`
- `GET http://localhost:8000/api/bulletins/classe/1/periode/Semestre 1`

**Une fois implémenté, ton frontend Angular pourra récupérer les vraies données de la base de données !** 