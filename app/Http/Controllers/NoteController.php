<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = \App\Models\Note::with(['eleve', 'matiere', 'enseignant'])
            ->whereHas('eleve')
            ->whereHas('matiere')
            ->whereHas('enseignant')
            ->get();
        return response()->json($notes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $enseignant = null;
        $enseignant_id = $request->input('enseignant_id');
        
        if ($user->role === 'enseignant') {
            $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
            $enseignant_id = $enseignant ? $enseignant->id : null;
        } elseif ($enseignant_id) {
            $enseignant = \App\Models\Enseignant::find($enseignant_id);
        }

        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'valeur' => 'required|numeric|min:0|max:20',
            'periode' => 'required|in:Semestre 1,Semestre 2',
            'enseignant_id' => 'nullable|exists:enseignants,id',
        ]);

        // Vérifier que l'enseignant a le droit de noter cet élève/matière
        $eleve = \App\Models\Eleve::find($validated['eleve_id']);
        $matiereId = $validated['matiere_id'];
        
        if ($user->role === 'enseignant') {
            // Vérifier que l'enseignant enseigne cette matière dans la classe de l'élève
            $enseigne = $enseignant && $eleve ? \DB::table('enseignant_matiere')
                ->where('enseignant_id', $enseignant_id)
                ->where('matiere_id', $matiereId)
                ->where('classe_id', $eleve->classe_id)
                ->exists() : false;
                
            if (!$enseigne) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas noter cet élève pour cette matière. Vérifiez que vous enseignez cette matière dans la classe de l\'élève.'
                ], 403);
            }
        }

        // Vérifier si une note existe déjà pour cet élève/matière/période
        $noteExistante = \App\Models\Note::where('eleve_id', $validated['eleve_id'])
            ->where('matiere_id', $matiereId)
            ->where('periode', $validated['periode'])
            ->where('enseignant_id', $enseignant_id)
            ->first();
            
        if ($noteExistante) {
            return response()->json([
                'message' => 'Une note existe déjà pour cet élève, cette matière et cette période.'
            ], 409);
        }

        $note = \App\Models\Note::create([
            'eleve_id' => $validated['eleve_id'],
            'matiere_id' => $matiereId,
            'enseignant_id' => $enseignant_id,
            'valeur' => $validated['valeur'],
            'periode' => $validated['periode'],
        ]);

        $note = \App\Models\Note::with(['eleve', 'matiere', 'enseignant'])->find($note->id);
        return response()->json($note, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $note = Note::find($id);
        return response()->json($note, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'eleve_id' => 'sometimes|exists:eleves,id',
            'matiere_id' => 'sometimes|exists:matieres,id',
            'enseignant_id' => 'sometimes|exists:enseignants,id',
            'valeur' => 'sometimes|numeric|min:0|max:20',
            'periode' => 'sometimes|in:Semestre 1,Semestre 2',
        ]);
        $note = \App\Models\Note::findOrFail($id);
        $note->update($validated);
        // Retourner la note modifiée avec ses relations
        $note = \App\Models\Note::with(['eleve', 'matiere', 'enseignant'])->find($id);
        return response()->json($note, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = \App\Models\Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note introuvable'], 404);
        }
        $note->delete();
        return response()->json(['message' => 'Note supprimée avec succès'], 200);
    }

    public function mesNotes(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'enseignant') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $enseignant = \App\Models\Enseignant::where('user_id', $user->id)->first();
        $notes = \App\Models\Note::with(['eleve', 'matiere'])
            ->where('enseignant_id', $enseignant->id)
            ->get();
        return response()->json($notes);
    }
}
