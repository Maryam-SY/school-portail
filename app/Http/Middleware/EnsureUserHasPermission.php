<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Eleve;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $model, $action)
    {
        $user = $request->user();

        // Vérification des permissions basées sur le rôle et le modèle
        switch ($model) {
            case 'eleve':
                if ($action === 'view') {
                    // Un parent/élève ne peut voir que ses propres données
                    $eleveId = $request->route('id') ?? $request->route('eleve');
                    $eleve = Eleve::findOrFail($eleveId);
                    
                    if ($user->role === 'eleve' && $eleve->email !== $user->email) {
                        return response()->json(['message' => 'Accès non autorisé'], 403);
                    }
                    
                    if ($user->role === 'parent') {
                        // Pour l'instant, les parents ont accès aux bulletins via l'email
                        $eleve = Eleve::where('email', $user->email)->first();
                        if (!$eleve || $eleve->id != $eleveId) {
                            return response()->json(['message' => 'Accès non autorisé'], 403);
                        }
                    }
                }
                break;
            
            case 'note':
                if ($action === 'create') {
                    // Seuls les enseignants peuvent créer des notes
                    if ($user->role !== 'enseignant') {
                        return response()->json(['message' => 'Accès non autorisé'], 403);
                    }
                }
                break;
            
            case 'bulletin':
                if ($action === 'generate') {
                    // Seuls admin et enseignants peuvent générer des bulletins
                    if (!in_array($user->role, ['admin', 'enseignant'])) {
                        return response()->json(['message' => 'Accès non autorisé'], 403);
                    }
                }
                break;
        }

        return $next($request);
    }
}
