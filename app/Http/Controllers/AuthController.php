<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Auditeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin()
    {
        // Rediriger si déjà connecté
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('pages.auth.signin', ['title' => 'Connexion']);
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Chercher l'utilisateur par email
        $user = Utilisateur::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            // Vérifier si le compte est actif
            if (!$user->is_active) {
                return back()->withErrors([
                    'email' => 'Ce compte est désactivé.',
                ])->onlyInput('email');
            }
            
            // Mettre à jour la dernière connexion
            $user->update(['derniere_connexion' => now()]);
            
            // Connecter l'utilisateur
            Auth::login($user, $request->has('remember'));
            
            // Rediriger vers le dashboard
            return redirect()->intended('/dashboard')->with('success', 'Bienvenue ' . $user->username);
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas.',
        ])->onlyInput('email');
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegister()
    {
        // Rediriger si déjà connecté
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('pages.auth.signup', ['title' => 'Inscription']);
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:utilisateurs,username',
            'email' => 'required|email|max:255|unique:utilisateurs,email',
            'password' => 'required|min:6|confirmed',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ]);

        // Créer l'utilisateur
        $utilisateur = Utilisateur::create([
            'id' => Str::uuid(),
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role' => 'auditeur',
            'niveau' => 'debutant',
            'progression_globale' => 0,
            'is_active' => true,
            'date_inscription' => now(),
        ]);

        // Créer le profil auditeur
        Auditeur::create([
            'id' => Str::uuid(),
            'utilisateur_id' => $utilisateur->id,
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'telephone' => $validated['telephone'] ?? null,
            'certification' => null,
            'experience_ans' => 0,
            'specialites' => json_encode([]),
        ]);

        // Connecter l'utilisateur
        Auth::login($utilisateur);

        return redirect()->route('dashboard')->with('success', 'Inscription réussie ! Bienvenue ' . $utilisateur->username);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Vous êtes déconnecté.');
    }
}