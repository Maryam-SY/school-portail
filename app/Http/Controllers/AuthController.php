<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Inscription
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,enseignant,eleve'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations sont incorrectes.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function registerEleveParent(Request $request)
    {
        // Accepte email_eleve/email_parent ou email/parent_email
        $data = $request->all();
        $data['email'] = $data['email'] ?? $data['email_eleve'] ?? null;
        $data['parent_email'] = $data['parent_email'] ?? $data['email_parent'] ?? null;

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'parent_email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Cet email élève est déjà utilisé.',
            'parent_email.unique' => 'Cet email parent est déjà utilisé.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        // Crée le user parent
        $userParent = \App\Models\User::create([
            'name' => 'Parent de ' . $validated['prenom'],
            'email' => $validated['parent_email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'parent',
        ]);

        // Crée le user élève
        $userEleve = \App\Models\User::create([
            'name' => $validated['prenom'] . ' ' . $validated['nom'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'eleve',
        ]);

        // Crée l’élève et associe le parent
        $eleve = \App\Models\Eleve::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'date_naissance' => $validated['date_naissance'],
            'email' => $validated['email'],
            'parent_email' => $validated['parent_email'],
            'parent_user_id' => $userParent->id,
            // autres champs si besoin
        ]);

        return response()->json([
            'email_eleve' => $userEleve->email,
            'email_parent' => $userParent->email,
            'password' => $validated['password'],
        ]);
    }
}
