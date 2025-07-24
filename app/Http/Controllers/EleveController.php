<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\Request;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Eleve::with('classe')->get();
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
            'classe_id' => 'required|exists:classes,id',
            'date_naissance' => 'required|date',
            'email' => 'required|email|unique:eleves,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500'
        ]);

        if($validated){
            // Générer un identifiant unique
            $identifiant = 'ELEVE-' . date('Y') . '-' . str_pad(Eleve::count() + 1, 4, '0', STR_PAD_LEFT);
            $validated['identifiant'] = $identifiant;

            $eleve = Eleve::create($validated);
            return response()->json($eleve, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $eleve = \App\Models\Eleve::with(['classe', 'classe.enseignants', 'notes.matiere', 'notes.enseignant'])->findOrFail($id);
        $notes = $eleve->notes;
        $moyenne = $notes->count() ? round($notes->avg('valeur'), 2) : null;
        $mention = app(\App\Http\Controllers\BulletinController::class)->getMention($moyenne);
        $rang = null; // Optionnel : à calculer si besoin
        return response()->json([
            'id' => $eleve->id,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'classe' => $eleve->classe,
            'notes' => $notes,
            'moyenne' => $moyenne,
            'mention' => $mention,
            'rang' => $rang,
            'enseignants' => $eleve->classe->enseignants ?? [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:eleves,id',
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'classe_id' => 'sometimes|exists:classes,id',
            'date_naissance' => 'sometimes|date',
            'email' => 'sometimes|email|unique:eleves,email,' . $request["id"],
            'telephone' => 'sometimes|nullable|string|max:20',
            'adresse' => 'sometimes|nullable|string|max:500'
        ]);

        if($validated){
            $eleve = Eleve::find($request["id"]);
            $eleve->update($validated);
            return response()->json($eleve, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Eleve::destroy($id);
        return response()->json("Élève supprimé avec succès", 200);
    }

    public function infos()
    {
        $user = auth()->user();
        $eleve = \App\Models\Eleve::where('user_id', $user->id)
            ->with(['classe', 'classe.enseignants', 'notes.matiere'])
            ->firstOrFail();

        $notes = $eleve->notes;
        $moyenne = $notes->count() ? round($notes->avg('valeur'), 2) : null;
        $mention = app(\App\Http\Controllers\BulletinController::class)->getMention($moyenne);
        $rang = null; // Optionnel : à calculer si besoin

        return response()->json([
            'eleve' => $eleve,
            'classe' => $eleve->classe,
            'enseignants' => $eleve->classe->enseignants ?? [],
            'notes' => $notes,
            'moyenne' => $moyenne,
            'mention' => $mention,
            'rang' => $rang,
        ]);
    }

    public function mesBulletins()
    {
        $user = auth()->user();
        $eleve = \App\Models\Eleve::where('user_id', $user->id)->firstOrFail();
        $periodes = $eleve->notes()->distinct('periode')->pluck('periode');
        $bulletins = [];
        foreach ($periodes as $periode) {
            $bulletin = app(\App\Http\Controllers\BulletinController::class)->genererBulletin($eleve->id, $periode);
            if ($bulletin) {
                $bulletins[] = $bulletin;
            }
        }
        return response()->json($bulletins);
    }
}
