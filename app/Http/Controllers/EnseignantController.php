<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Enseignant::with(['matieres', 'classes'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'specialite' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            'matiere_ids' => 'required|array|min:1',
            'classe_ids' => 'required|array|min:1',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
        ]);

        // Générer un mot de passe temporaire
        $tempPassword = Str::random(10);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['prenom'] . ' ' . $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'enseignant',
        ]);

        // Créer l'enseignant lié à l'utilisateur
        $enseignant = Enseignant::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'specialite' => $validated['specialite'],
            'date_embauche' => $validated['date_embauche'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'user_id' => $user->id,
            // NE PAS inclure 'adresse' si la colonne n'existe pas !
        ]);

        $enseignant->matieres()->sync($validated['matiere_ids']);
        $enseignant->classes()->sync($validated['classe_ids']);

        // Retourner le mot de passe généré pour affichage/email
        return response()->json([
            'enseignant' => $enseignant->load(['matieres', 'classes']),
            'user' => $user,
            'temp_password' => $tempPassword
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Enseignant::with(['matieres', 'classes'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $enseignant = Enseignant::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email',
            'specialite' => 'required|string',
            'date_embauche' => 'required|date',
            'matiere_ids' => 'required|array|min:1',
            'classe_ids' => 'required|array|min:1',
        ]);

        // On ne passe que les champs scalaires à update()
        $enseignant->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'specialite' => $validated['specialite'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        // Suppression des anciennes associations dans la table pivot
        \DB::table('enseignant_matiere')->where('enseignant_id', $enseignant->id)->delete();
        // Réinsertion des nouvelles associations
        foreach ($validated['matiere_ids'] as $matiere_id) {
            foreach ($validated['classe_ids'] as $classe_id) {
                \DB::table('enseignant_matiere')->insert([
                    'enseignant_id' => $enseignant->id,
                    'matiere_id' => $matiere_id,
                    'classe_id' => $classe_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json($enseignant->load(['matieres', 'classes']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Enseignant::destroy($id);
        return response()->json("Enseignant supprimé avec succès", 200);
    }

    /**
     * Retourne les classes de l'enseignant connecté
     */
    public function mesClasses(Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $classes = $enseignant->classes()->distinct()->get();
        return response()->json($classes);
    }

    /**
     * Retourne les matières de l'enseignant connecté
     */
    public function mesMatieres(Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $matieres = $enseignant->matieres()->distinct()->get();
        return response()->json($matieres);
    }

    /**
     * Retourne les élèves d'une classe si l'enseignant y enseigne
     */
    public function elevesClasse($classe_id, Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseigne = $enseignant->classes()->where('classes.id', $classe_id)->exists();
        if (!$enseigne) {
            return response()->json(['message' => 'Vous n\'enseignez pas dans cette classe'], 403);
        }
        $classe = \App\Models\Classe::with('eleves')->findOrFail($classe_id);
        return response()->json($classe->eleves);
    }

    // Retourne les classes affectées à l'enseignant (par id)
    public function classesById($id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== (int)$id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseignant = \App\Models\Enseignant::findOrFail($id);
        $classes = $enseignant->classes()->get();
        return response()->json($classes);
    }

    // Retourne les matières affectées à l'enseignant (par id)
    public function matieresById($id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== (int)$id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseignant = \App\Models\Enseignant::findOrFail($id);
        $matieres = $enseignant->matieres()->get();
        return response()->json($matieres);
    }

    // Retourne les élèves d'une classe d'un enseignant (par id)
    public function elevesByClasse($id, $classe_id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== (int)$id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseignant = \App\Models\Enseignant::findOrFail($id);
        $classe = $enseignant->classes()->where('classes.id', $classe_id)->first();
        if (!$classe) {
            return response()->json(['message' => 'Classe non autorisée'], 403);
        }
        $eleves = \App\Models\Eleve::where('classe_id', $classe_id)->get();
        return response()->json($eleves);
    }

    // Retourne les matières de l'enseignant pour une classe donnée (optionnel)
    public function matieresByClasse($id, $classe_id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== (int)$id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseignant = \App\Models\Enseignant::findOrFail($id);
        $matieres = $enseignant->matieres()->wherePivot('classe_id', $classe_id)->get();
        return response()->json($matieres);
    }

    /**
     * Statistiques pour l'enseignant connecté (exemple minimal)
     */
    public function stats(Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        // Nombre de classes et matières
        $nbClasses = $enseignant->classes()->distinct()->count();
        $nbMatieres = $enseignant->matieres()->distinct()->count();
        $nbNotes = \App\Models\Note::where('enseignant_id', $enseignant->id)->count();
        $nbBulletins = 0; // À implémenter si nécessaire
        
        return response()->json([
            'nb_classes' => $nbClasses,
            'nb_matieres' => $nbMatieres,
            'nb_notes' => $nbNotes,
            'nb_bulletins' => $nbBulletins
        ]);
    }

    /**
     * Notes saisies par l'enseignant connecté (exemple minimal)
     */
    public function notes(Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $notes = \App\Models\Note::with(['eleve', 'matiere'])
            ->where('enseignant_id', $enseignant->id)
            ->get();
        return response()->json($notes);
    }

    /**
     * Retourne tous les élèves de l'enseignant connecté (de toutes ses classes)
     */
    public function mesEleves(Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Récupérer les IDs des classes où l'enseignant enseigne
        $classeIds = $enseignant->classes()->pluck('classes.id');
        
        // Récupérer tous les élèves de ces classes
        $eleves = \App\Models\Eleve::with('classe')
            ->whereIn('classe_id', $classeIds)
            ->get();
            
        return response()->json($eleves);
    }

    /**
     * Retourne les élèves d'une classe spécifique (si l'enseignant y enseigne)
     */
    public function elevesParClasse($classe_id, Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Vérifier que l'enseignant enseigne dans cette classe
        $enseigne = $enseignant->classes()->where('classes.id', $classe_id)->exists();
        if (!$enseigne) {
            return response()->json(['message' => 'Vous n\'enseignez pas dans cette classe'], 403);
        }
        
        // Récupérer les élèves de cette classe
        $eleves = \App\Models\Eleve::with('classe')
            ->where('classe_id', $classe_id)
            ->get();
            
        return response()->json($eleves);
    }

    /**
     * Retourne les élèves pour une matière spécifique (selon les classes où l'enseignant enseigne cette matière)
     */
    public function elevesParMatiere($matiere_id, Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Récupérer les classes où l'enseignant enseigne cette matière
        $classeIds = \DB::table('enseignant_matiere')
            ->where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $matiere_id)
            ->pluck('classe_id');
        
        if ($classeIds->isEmpty()) {
            return response()->json(['message' => 'Vous n\'enseignez pas cette matière'], 403);
        }
        
        // Récupérer les élèves de ces classes
        $eleves = \App\Models\Eleve::with('classe')
            ->whereIn('classe_id', $classeIds)
            ->get();
            
        return response()->json($eleves);
    }

    /**
     * Retourne les élèves pour une combinaison classe + matière
     */
    public function elevesParClasseEtMatiere($classe_id, $matiere_id, Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Vérifier que l'enseignant enseigne cette matière dans cette classe
        $enseigne = \DB::table('enseignant_matiere')
            ->where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $matiere_id)
            ->where('classe_id', $classe_id)
            ->exists();
        
        if (!$enseigne) {
            return response()->json(['message' => 'Vous n\'enseignez pas cette matière dans cette classe'], 403);
        }
        
        // Récupérer les élèves de cette classe
        $eleves = \App\Models\Eleve::with('classe')
            ->where('classe_id', $classe_id)
            ->get();
            
        return response()->json($eleves);
    }

    /**
     * Retourne les matières enseignées dans une classe spécifique
     */
    public function matieresParClasse($classe_id, Request $request)
    {
        $user = auth()->user();
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        if (!$enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Vérifier que l'enseignant enseigne dans cette classe
        $enseigne = $enseignant->classes()->where('classes.id', $classe_id)->exists();
        if (!$enseigne) {
            return response()->json(['message' => 'Vous n\'enseignez pas dans cette classe'], 403);
        }
        
        // Récupérer les matières enseignées dans cette classe
        $matieres = \App\Models\Matiere::whereHas('enseignants', function($query) use ($enseignant, $classe_id) {
            $query->where('enseignant_id', $enseignant->id)
                  ->where('classe_id', $classe_id);
        })->get();
            
        return response()->json($matieres);
    }
}
