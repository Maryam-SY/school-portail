<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Eleve;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    // Retourne la liste des enfants du parent connecté (avec classe, notes, matières)
    public function enfants()
    {
        $parent = Auth::user();
        // On suppose que la relation enfants() existe sur User
        $enfants = Eleve::where('parent_user_id', $parent->id)
            ->with(['classe', 'notes.matiere'])
            ->get();
        return response()->json($enfants);
    }

    // Retourne la liste des bulletins pour un enfant
    public function bulletins($enfantId)
    {
        $parent = Auth::user();
        $enfant = Eleve::where('parent_user_id', $parent->id)->findOrFail($enfantId);
        // On suppose que la méthode genererBulletin existe dans BulletinController
        $bulletins = [];
        $periodes = $enfant->notes()->distinct('periode')->pluck('periode');
        foreach ($periodes as $periode) {
            $bulletin = app(\App\Http\Controllers\BulletinController::class)->genererBulletin($enfant->id, $periode);
            if ($bulletin) {
                $bulletins[] = $bulletin;
            }
        }
        return response()->json($bulletins);
    }
} 