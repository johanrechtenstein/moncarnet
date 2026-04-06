<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Pour crypter le mot de passe
use Illuminate\Support\Facades\Auth; // Pour gérer l'authentification
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        // 1. On valide que l'utilisateur a bien envoyé un email et un password
        $request->validate([
            'pseudo' => 'required|string',
            'password' => 'required',
        ]);

        // 2. On tente de connecter l'utilisateur (Laravel compare l'email et le hash du mot de passe)
        if (!Auth::attempt($request->only('pseudo', 'password'))) {
            return response()->json([
                'message' => 'Identifiants incorrects'
            ], 401); // 401 = Non autorisé
        }

        // 3. Si on arrive ici, c'est que les identifiants sont bons !
        $user = User::where('pseudo', $request->pseudo)->firstOrFail();

        // Optionnel : Supprime tous les anciens tokens de cet utilisateur avant d'en créer un nouveau
        $user->tokens()->delete();

        // 4. On génère un NOUVEAU token pour cette session
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
        }


    public function register(Request $request)
    {
        // 1. La validation (on vérifie que l'email est unique et le mot de passe assez long)
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' attend un champ password_confirmation
        ]);

        // 2. On crée l'utilisateur en base
        $user = User::create([
            'pseudo' => $validated['pseudo'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // On crypte !
        ]);

        // 3. ON UTILISE LE SUPER-POUVOIR ICI !
        // On génère la "clé" pour cet utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. On renvoie le tout au client (Vue.js)
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        // On récupère l'utilisateur qui fait la demande et on supprime son token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }


public function destroy(Request $request)
{
    // 1. On valide que le mot de passe est présent dans la requête
    $request->validate([
        'password' => 'required|string',
    ]);

    $user = $request->user();

    // 2. On vérifie si le mot de passe correspond
    if (!Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'password' => ['Le mot de passe est incorrect. Suppression annulée.'],
        ]);
    }

    // 3. Si c'est bon, on nettoie les tokens et on supprime l'utilisateur
    $user->tokens()->delete();
    $user->delete();

    return response()->json([
        'message' => 'Votre compte et toutes vos données ont été supprimés.'
    ], 200);
}


public function update(Request $request)
{
    // Ici, on est sûr à 100% que $request->user() contient un utilisateur
    // car le middleware Sanctum a déjà fait le barrage en amont.
    $user = $request->user();

    $validated = $request->validate([
        'pseudo' => 'sometimes|string|unique:users,pseudo,' . $user->id,
        'email'  => 'sometimes|email|unique:users,email,' . $user->id,
    ]);

    $user->update($validated);

    // On renvoie l'utilisateur mis à jour
    return response()->json([
        'status' => 'success',
        'message' => 'Profil mis à jour !',
        'user' => $user // Important pour que Vue.js mette à jour son état
    ], 200);
}



public function checkEmail(Request $request)
{
    // On vérifie si l'email existe déjà dans la table users
    $exists = User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
}

public function checkPseudo(Request $request)
{
    // Idem pour le pseudo
    $exists = User::where('pseudo', $request->pseudo)->exists();

    return response()->json(['exists' => $exists]);
}



}
