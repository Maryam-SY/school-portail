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
        $notes = Note::all();
        return response()->json($notes, 200);
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
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'valeur' => 'required|numeric|min:0|max:20',
            'periode' => 'required|string|max:255',
            'type_evaluation' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string'
        ]);

        if($validated){
            $note = Note::create($validated);
            return response()->json($note, 201);
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
        $note = Note::find($id);
        return response()->json($note, 200);
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
            'id' => 'required|exists:notes,id',
            'eleve_id' => 'sometimes|exists:eleves,id',
            'matiere_id' => 'sometimes|exists:matieres,id',
            'enseignant_id' => 'sometimes|exists:enseignants,id',
            'valeur' => 'sometimes|numeric|min:0|max:20',
            'periode' => 'sometimes|string|max:255',
            'type_evaluation' => 'sometimes|nullable|string|max:255',
            'commentaire' => 'sometimes|nullable|string'
        ]);

        if($validated){
            $note = Note::find($request["id"]);
            $note->update($validated);
            return response()->json($note, 200);
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
        Note::destroy($id);
        return response()->json("Note supprimée avec succès", 200);
    }
}
