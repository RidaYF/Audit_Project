<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Preuve;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ControleController extends Controller
{
    /**
     * Afficher la liste des contrôles (pour l'admin)
     */
    public function index()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $controles = Controle::with('clause')->orderBy('code')->paginate(20);
        
        return view('pages.controles.index', [
            'title' => 'Gestion des contrôles ISO 27002',
            'controles' => $controles
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition d'un contrôle
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $controle = Controle::with('clause', 'preuves')->findOrFail($id);
        
        return view('pages.controles.edit', [
            'title' => 'Modifier le contrôle - ' . $controle->code,
            'controle' => $controle
        ]);
    }
    
    /**
     * Mettre à jour un contrôle
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $controle = Controle::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'objectif' => 'required|string',
            'categorie' => 'nullable|string|max:100',
            'niveau_critique' => 'required|in:faible,moyen,eleve,critique',
            'type_controle' => 'required|in:preventif,detectif,correctif,analyse',
            'ponderation_defaut' => 'required|integer|min:0|max:10',
            'temps_estime' => 'nullable|integer|min:0',
            'conseils_audit' => 'nullable|string',
        ]);
        
        $controle->update($validated);
        
        return redirect()->route('controles.index')
            ->with('success', 'Contrôle mis à jour avec succès !');
    }
    
    /**
     * Afficher les preuves d'un contrôle
     */
    public function preuves($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $controle = Controle::with('preuves')->findOrFail($id);
        
        return view('pages.controles.preuves', [
            'title' => 'Preuves du contrôle - ' . $controle->code,
            'controle' => $controle
        ]);
    }
    
    /**
     * Ajouter une preuve à un contrôle (support images et documents)
     */
    public function addPreuve(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $controle = Controle::findOrFail($id);
        
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'type_preuve' => 'required|string|in:document,configuration,log,entretien,capture_ecran,analyse,image',
            'contenu' => 'required|string',
            'points_max' => 'required|integer|min:0|max:10',
            'fichier' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xls,xlsx,txt', // 5MB max
        ]);
        
        $filePath = null;
        if ($request->hasFile('fichier')) {
            // Stocker le fichier (image ou document)
            $filePath = $request->file('fichier')->store('preuves/' . $controle->id, 'public');
        }
        
        Preuve::create([
            'id' => Str::uuid(),
            'controle_id' => $controle->id,
            'titre' => $validated['titre'],
            'type_preuve' => $validated['type_preuve'],
            'contenu' => $validated['contenu'],
            'points_max' => $validated['points_max'],
            'ordre_affichage' => Preuve::where('controle_id', $controle->id)->count() + 1,
            'is_active' => true,
            'file_path' => $filePath, // Utiliser file_path pour tous les fichiers
            // 'image_path' reste pour la compatibilité avec les anciennes données
        ]);
        
        return redirect()->back()->with('success', 'Preuve ajoutée avec succès !');
    }

    /**
     * Supprimer le fichier d'une preuve
     */
    public function deleteFile($preuveId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $preuve = Preuve::findOrFail($preuveId);
        
        // Vérifier et supprimer le fichier dans file_path
        if ($preuve->file_path && Storage::disk('public')->exists($preuve->file_path)) {
            Storage::disk('public')->delete($preuve->file_path);
            $preuve->update(['file_path' => null]);
            return redirect()->back()->with('success', 'Fichier supprimé avec succès !');
        }
        
        // Pour la compatibilité avec les anciennes images dans image_path
        if ($preuve->image_path && Storage::disk('public')->exists($preuve->image_path)) {
            Storage::disk('public')->delete($preuve->image_path);
            $preuve->update(['image_path' => null]);
            return redirect()->back()->with('success', 'Image supprimée avec succès !');
        }
        
        return redirect()->back()->with('error', 'Aucun fichier à supprimer.');
    }
    
    /**
     * Supprimer une preuve complète
     */
    public function deletePreuve($preuveId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'formateur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $preuve = Preuve::findOrFail($preuveId);
        
        // Supprimer le fichier associé s'il existe (file_path)
        if ($preuve->file_path && Storage::disk('public')->exists($preuve->file_path)) {
            Storage::disk('public')->delete($preuve->file_path);
        }
        
        // Supprimer l'ancienne image si elle existe (image_path) - compatibilité
        if ($preuve->image_path && Storage::disk('public')->exists($preuve->image_path)) {
            Storage::disk('public')->delete($preuve->image_path);
        }
        
        $preuve->delete();
        
        return redirect()->back()->with('success', 'Preuve supprimée avec succès !');
    }
}