<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Pour crypter le mot de passe
use Illuminate\Support\Facades\Auth; // Pour gérer l'authentification
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Mail\ContactMessage; 
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    

    public function sendContact(Request $request) 
{
    $data = $request->validate([
        'email'   => 'required|email:rfc,dns',
        'message' => 'required|string|min:10|max:5000',
    ]);
    // 2. Nettoyage XSS (On enlève les balises <script>, etc.)
    // strip_tags transforme "<b>Salut</b>" en "Salut"
    $data['message'] = strip_tags($data['message']);

    // 3. Envoi (On garde ta logique)
    try {
        Mail::to('maxitunnig67@gmail.com')->send(new ContactMessage($data));
        return response()->json(['message' => 'Message envoyé avec succès !']);
    } catch (\Exception $e) {
        // En cas de problème technique (serveur mail en panne)
        return response()->json(['message' => 'Désolé, l\'envoi a échoué.'], 500);
    }
}


    public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    // On utilise le moteur de Laravel pour valider le token et changer le mot de passe
    $status = Password::broker()->reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => $password // On ne hash pas le nouveau MDP, laravel s'en charge avec le model.
            ])->setRememberToken(Str::random(60));

            $user->save();
            $user->tokens()->delete();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Votre mot de passe a été réinitialisé !'])
        : response()->json(['message' => 'Lien invalide ou expiré.'], 400);
}


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

        // IMPORTANT : On régénère la session pour éviter le vol de session (Fixation)
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => Auth::user(),
        ]);
        // On ne renvoie PLUS de access_token, c'est le cookie qui fait tout le travail !
        }


    public function register(Request $request)
    {
        // 1. La validation (on vérifie que l'email est unique et le mot de passe assez long)
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' attend un champ password_confirmation
        ]);

        // 2. On crée l'utilisateur en base
        $user = User::create([
            'pseudo' => $validated['pseudo'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // On crypte !
        ]);

        // On connecte l'utilisateur tout de suite pour créer sa session
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // On réinitialise le jeton CSRF

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
            'password' => ['Le mot de passe est incorrect.'],
        ]);
    }

   // 1. Déconnexion manuelle
    Auth::guard('web')->logout();

    // 2. Destruction de la session (très important avant de supprimer l'user)
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // 3. Suppression de l'utilisateur en base
    $user->delete();

    return response()->json([
        'message' => 'Compte supprimé avec succès.'
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

public function sendResetLinkEmail(Request $request)
{
    // On valide que l'email est fourni
    $request->validate(['email' => 'required|email']);

    // On demande à Laravel d'envoyer le lien de réinitialisation
    $status = Password::broker()->sendResetLink(
        $request->only('email')
    );

    // Si tout est bon, Laravel renvoie un code de succès
    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Lien de réinitialisation envoyé ! Vérifiez votre boîte mail.'])
        : response()->json(['message' => 'Impossible d\'envoyer le mail.'], 400);
}

}
