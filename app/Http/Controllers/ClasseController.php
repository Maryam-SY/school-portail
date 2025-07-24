<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = Classe::all();
        return response()->json($classes, 200);
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
            'nom' => 'required|string',
            'niveau' => 'required|string',
            'annee_scolaire' => 'required|string',
            'capacite' => 'required|integer',
        ]);
        $classe = Classe::create($validated);
        return response()->json($classe, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $classe = Classe::find($id);
        return response()->json($classe, 200);
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
            'nom' => 'sometimes|required|string',
            'niveau' => 'sometimes|required|string',
            'annee_scolaire' => 'sometimes|required|string',
            'capacite' => 'sometimes|required|integer',
        ]);
        $classe = Classe::findOrFail($id);
        $classe->update($validated);
        return response()->json($classe, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Classe::destroy($id);
        return response()->json("Classe supprimée avec succès", 200);
    }
}
