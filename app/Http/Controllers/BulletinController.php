<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Note;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipStream\ZipStream;
use ZipStream\OperationMode;
use GuzzleHttp\Psr7\Utils;


class BulletinController extends Controller
{
    /**
     * Afficher tous les bulletins (pour administrateurs/enseignants)
     */
    public function index()
    {
        // Récupérer tous les élèves avec leur classe
        $eleves = Eleve::with('classe')->get();
        $bulletins = [];

        // Générer un bulletin pour chaque élève
        foreach ($eleves as $eleve) {
            $bulletin = $this->genererBulletin($eleve->id);
            if ($bulletin) {
                $bulletins[] = $bulletin;
            }
        }

        return response()->json($bulletins, 200);
    }

    /**
     * Créer un nouveau bulletin (pour administrateurs)
     */
    public function store(Request $request)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id', 
            'periode' => 'required|string|max:255'     
        ]);

        if($validated){
            $bulletin = $this->genererBulletin($validated['eleve_id'], $validated['periode']);
            return response()->json($bulletin, 201); 
        }
    }

  
    public function show($id)
    {
        // Vérifier que l'élève existe
        $eleve = Eleve::find($id);
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        // Générer le bulletin pour cet élève
        $bulletin = $this->genererBulletin($id);
        return response()->json($bulletin, 200);
    }

    /**
     * Modifier un bulletin existant (pour administrateurs)
     */
    public function update(Request $request)
    {
        // Validation des données de mise à jour
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'periode' => 'required|string|max:255'
        ]);

        if($validated){
            // Régénérer le bulletin avec les nouvelles données
            $bulletin = $this->genererBulletin($validated['eleve_id'], $validated['periode']);
            return response()->json($bulletin, 200);
        }
    }

    /**
     * Supprimer un bulletin 
     */
    public function destroy($id)
    {
        return response()->json("Suppression non autorisée", 403);
    }

    /**
     * Générer un bulletin simple pour un élève
     */
    public function genererBulletin($eleve_id, $periode = null)
    {
        
        $eleve = Eleve::with('classe')->find($eleve_id);
        if (!$eleve) {
            return null; 
        }

        if (!$periode) {
            $periode = Note::where('eleve_id', $eleve_id)
                          ->orderBy('created_at', 'desc')
                          ->value('periode');
            
            if (!$periode) {
                return null; 
            }
        }
        $notes = Note::with('matiere') 
                    ->where('eleve_id', $eleve_id)
                    ->where('periode', $periode)
                    ->get();
// verifier si nullll
        if ($notes->isEmpty()) {
            return null;
        }

        $somme = 0;
        $nbNotes = count($notes);
        
        foreach ($notes as $note) {
            $somme += $note->valeur; 
        }
        
        $moyenne = $nbNotes > 0 ? round($somme / $nbNotes, 2) : 0;

        $rang = $this->calculerRang($eleve_id, $eleve->classe_id, $periode);

        $mention = $this->getMention($moyenne);

        // Construire le bulletin final
        $bulletin = [
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom
            ],
            'classe' => $eleve->classe->nom,
            'periode' => $periode,
            'moyenne' => $moyenne,
            'rang' => $rang,
            'mention' => $mention,
            'notes' => $notes->map(function($note) { // Transforme chaque note en tableau 
                return [
                    'matiere' => $note->matiere->nom,
                    'note' => $note->valeur,
                    'type' => $note->type_evaluation
                ];
            })
        ];

        return $bulletin;
    }
    
    /**
     * Récupérer le bulletin de l'élève connecté (pour élèves/parents)
     */
    public function monBulletin()
    {
        $eleve = Eleve::where('email', Auth::user()->email)->first();
        
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        $bulletin = $this->genererBulletin($eleve->id);
        return response()->json($bulletin, 200);
    }

    /**
     * Générer les bulletins pour tous les élèves d'une classe
     */
    public function bulletinsClasse(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'required|string'
        ]);

        // Récupérer tous les élèves de la classe
        $eleves = Eleve::where('classe_id', $validated['classe_id'])->get();
        $bulletins = [];

        foreach ($eleves as $eleve) {
            $bulletin = $this->genererBulletin($eleve->id, $validated['periode']);
            if ($bulletin) {
                $bulletins[] = $bulletin;
            }
        }

        return response()->json([
            'classe_id' => $validated['classe_id'],
            'periode' => $validated['periode'],
            'bulletins' => $bulletins
        ], 200);
    }

    /**
     * Obtenir les statistiques académiques
     */
    public function statistiques()
    {
        // Statistiques globales
        $eleves = Eleve::count(); 
        $classes = Classe::count(); 
        $notes = Note::count();

        // Statistiques académiques par classe
        $statistiquesClasses = [];
        $classesList = Classe::all(); 
        foreach ($classesList as $classe) {
            $elevesClasse = Eleve::where('classe_id', $classe->id)->get();
            $moyennesClasse = [];

            // Calcule la moyenne de chaque élève
            foreach ($elevesClasse as $eleve) {
                $notesEleve = Note::where('eleve_id', $eleve->id)->get();
                if ($notesEleve->isNotEmpty()) {
                    $moyenne = round($notesEleve->sum('valeur') / count($notesEleve), 2); 
                    $moyennesClasse[] = $moyenne;
                }
            }

            // Calcule la moyenne de la classe
            $moyenneClasse = !empty($moyennesClasse) ? round(array_sum($moyennesClasse) / count($moyennesClasse), 2) : 0;

            $statistiquesClasses[] = [
                'classe' => $classe->nom,
                'nombre_eleves' => count($elevesClasse),
                'moyenne_classe' => $moyenneClasse
            ];
        }

        return response()->json([
            'nombre_eleves' => $eleves,
            'nombre_classes' => $classes,
            'nombre_notes' => $notes,
            'statistiques_par_classe' => $statistiquesClasses
        ], 200);
    }

    /**
     * Télécharger un bulletin en PDF
     */
    public function telechargerBulletinPDF($eleve_id, $periode = null)
    {
        // Vérifier les droits d'accès
        $user = Auth::user();
        if ($user->role === 'eleve') {
            $eleve = Eleve::where('email', $user->email)->first();
            if (!$eleve || $eleve->id != $eleve_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        }

        // Générer le bulletin
        $bulletin = $this->genererBulletin($eleve_id, $periode);
        
        if (!$bulletin) {
            return response()->json(['message' => 'Bulletin non trouvé'], 404);
        }

       
        $pdf = PDF::loadView('bulletins.pdf', compact('bulletin'));
        
        
        $filename = 'bulletin_' . $bulletin['eleve']['nom'] . '_' . $bulletin['periode'] . '.pdf';
        
       
        return $pdf->download($filename);
    }

    /**
     * Télécharger tous les bulletins d'une classe en ZIP
     */
    public function telechargerBulletinsGroupe(Request $request)
    {
        
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'periode' => 'required|string'
        ]);

        // Récupérer la classe
        $classe = Classe::find($validated['classe_id']);
        $eleves = Eleve::where('classe_id', $validated['classe_id'])->get();
        
        if ($eleves->isEmpty()) {
            return response()->json(['message' => 'Aucun élève trouvé dans cette classe'], 404);
        }

        $zipName = 'bulletins_' . $classe->nom . '_' . $validated['periode'] . '.zip';
        $bulletinsGenerated = 0;

        // Configurer les en-têtes HTTP pour forcer le téléchargement et CORS
        $this->setDownloadHeaders($zipName);

        // Créer le ZIP avec ZipStream
        $zip = new ZipStream(outputName: $zipName, operationMode: OperationMode::NORMAL);

        // Générer les bulletins PDF et les ajouter au ZIP
        foreach ($eleves as $eleve) {
            try {
                $bulletin = $this->genererBulletin($eleve->id, $validated['periode']);
                
                if ($bulletin) {
                    // Générer le PDF pour cet élève
                    $pdf = PDF::loadView('bulletins.pdf', compact('bulletin'));
                    $pdfContent = $pdf->output();
                    
                    // Nom du fichier PDF dans le ZIP
                    $pdfName = 'bulletin_' . $eleve->nom . '_' . $eleve->prenom . '_' . $validated['periode'] . '.pdf';
                    
                    // Ajouter le PDF au ZIP
                    $zip->addFileFromPsr7Stream(
                        fileName: $pdfName,
                        stream: Utils::streamFor($pdfContent)
                    );
                    
                    $bulletinsGenerated++;
                }
            } catch (\Exception $e) {
                // En cas d'erreur lors de la génération d'un bulletin, on continue avec les autres
                \Log::error('Erreur lors de la génération du bulletin pour l\'élève ' . $eleve->id . ': ' . $e->getMessage());
                continue;
            }
        }

        // Vérifier qu'au moins un bulletin a été généré
        if ($bulletinsGenerated === 0) {
            return response()->json(['message' => 'Aucun bulletin trouvé pour cette classe et cette période'], 404);
        }

        // ZipStream gère automatiquement l'envoi de la réponse HTTP
        $zip->finish();
        
        // Ne pas retourner de réponse car ZipStream l'a déjà fait
        exit();
    }



    /**
     * Historique des bulletins d'un élève
     */
    public function historiqueBulletins($eleve_id, $annee = null)
    {
        $user = Auth::user();
        if ($user->role === 'eleve') {
            $eleve = Eleve::where('email', $user->email)->first();
            if (!$eleve || $eleve->id != $eleve_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        }

        $query = Note::where('eleve_id', $eleve_id)->distinct('periode'); 
        
        if ($annee) {
            $query->where('periode', 'like', '%' . $annee . '%');
        }

        // Récupère les périodes uniques, les trie et les inverse (plus récent en premier)
        $periodes = $query->pluck('periode')->sort()->reverse();
        
        $historique = [];

        // Générer l'historique
        foreach ($periodes as $periode) {
            $bulletin = $this->genererBulletin($eleve_id, $periode);
            if ($bulletin) {
                $historique[] = [
                    'periode' => $periode,
                    'moyenne' => $bulletin['moyenne'],
                    'mention' => $bulletin['mention'],
                    'rang' => $bulletin['rang']
                ];
            }
        }

        return response()->json([
            'eleve_id' => $eleve_id,
            'historique' => $historique
        ], 200);
    }

    /**
     * Calculer le rang d'un élève dans sa classe
     */
    private function calculerRang($eleve_id, $classe_id, $periode)
    {
        // Récupère seulement les IDs des élèves de la classe
        $elevesClasse = Eleve::where('classe_id', $classe_id)->pluck('id');
        $moyennes = [];

        // Calcule la moyenne de chaque élève
        foreach ($elevesClasse as $eleveId) {
            $notes = Note::where('eleve_id', $eleveId)
                        ->where('periode', $periode)
                        ->get();

            if ($notes->isNotEmpty()) {
                $somme = $notes->sum('valeur'); 
                $moyenne = round($somme / count($notes), 2);
                $moyennes[$eleveId] = $moyenne;
            }
        }

        // Trie par moyenne décroissante 
        arsort($moyennes);

        $rang = 1;
        foreach ($moyennes as $id => $moyenne) {
            if ($id == $eleve_id) {
                return $rang; 
            }
            $rang++;
        }

        return count($moyennes); 
    }

    /**
     * Configurer les en-têtes HTTP pour le téléchargement et CORS
     */
    private function setDownloadHeaders($filename)
    {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    /**
     * Déterminer la mention selon la moyenne
     */
    private function getMention($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Très bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }

    /**
     * Notifier par email la disponibilité d'un bulletin
     */
    public function notifierBulletinDisponible($eleve_id, $periode)
    {
        // Vérifier les droits d'accès
        $user = Auth::user();
        if ($user->role === 'eleve') {
            $eleve = Eleve::where('email', $user->email)->first();
            if (!$eleve || $eleve->id != $eleve_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        }

        // Récupérer l'élève
        $eleve = Eleve::find($eleve_id);
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        // Vérifier que le bulletin existe
        $bulletin = $this->genererBulletin($eleve_id, $periode);
        if (!$bulletin) {
            return response()->json(['message' => 'Bulletin non trouvé pour cette période'], 404);
        }

        try {
            // Envoyer l'email de notification
            \Mail::to($eleve->email)->send(new \App\Mail\BulletinDisponible($eleve, $periode));
            
            return response()->json([
                'message' => 'Notification envoyée avec succès',
                'eleve' => $eleve->nom . ' ' . $eleve->prenom,
                'periode' => $periode,
                'email' => $eleve->email
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

