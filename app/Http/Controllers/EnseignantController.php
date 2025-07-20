<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\Request;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $enseignants = Enseignant::all();
        return response()->json($enseignants, 200);
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
            'email' => 'required|email|unique:enseignants,email',
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            'adresse' => 'nullable|string|max:500'
        ]);

        if($validated){
            $enseignant = Enseignant::create($validated);
            return response()->json($enseignant, 201);
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
        $enseignant = Enseignant::find($id);
        return response()->json($enseignant, 200);
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
            'id' => 'required|exists:enseignants,id',
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:enseignants,email,' . $request["id"],
            'telephone' => 'sometimes|nullable|string|max:20',
            'specialite' => 'sometimes|string|max:255',
            'date_embauche' => 'sometimes|date',
            'adresse' => 'sometimes|nullable|string|max:500'
        ]);

        if($validated){
            $enseignant = Enseignant::find($request["id"]);
            $enseignant->update($validated);
            return response()->json($enseignant, 200);
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
        Enseignant::destroy($id);
        return response()->json("Enseignant supprimé avec succès", 200);
    }
}
