<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $matieres = Matiere::all();
        return response()->json($matieres, 200);
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
            'code' => 'required|string|max:50|unique:matieres,code',
            'niveau' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coefficient' => 'required|numeric|min:0.1|max:10'
        ]);

        if($validated){
            $matiere = Matiere::create($validated);
            return response()->json($matiere, 201);
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
        $matiere = Matiere::find($id);
        return response()->json($matiere, 200);
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
            'id' => 'required|exists:matieres,id',
            'nom' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:matieres,code,' . $request["id"],
            'niveau' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'coefficient' => 'sometimes|numeric|min:0.1|max:10'
        ]);

        if($validated){
            $matiere = Matiere::find($request["id"]);
            $matiere->update($validated);
            return response()->json($matiere, 200);
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
        Matiere::destroy($id);
        return response()->json("Matière supprimée avec succès", 200);
    }
}
