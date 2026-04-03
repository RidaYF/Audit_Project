<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EntrepriseController extends Controller
{
    /**
     * Affiche la liste des entreprises avec filtres et statistiques
     */
    public function index(Request $request)
    {
        $query = Entreprise::query();
        
        // Filtre par recherche
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }
        
        // Filtre par secteur
        if ($request->filled('secteur')) {
            $query->where('secteur', $request->secteur);
        }
        
        // Filtre par taille
        if ($request->filled('taille')) {
            $query->where('taille', $request->taille);
        }
        
        // Tri par date de création décroissante
        $entreprises = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistiques
        $stats = [
            'total' => Entreprise::count(),
            'secteurs' => Entreprise::distinct('secteur')->count('secteur'),
            'effectif_total' => Entreprise::sum('effectif'),
            'actives' => Entreprise::where('is_active', true)->count(),
        ];
        
        return view('pages.entreprises.index', compact('entreprises', 'stats'));
    }
    
    /**
     * Affiche le formulaire de création d'une entreprise
     */
    public function create()
    {
        return view('pages.entreprises.create');
    }
    
    /**
     * Enregistre une nouvelle entreprise dans la base de données
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'secteur' => 'required|string|max:100',
            'taille' => 'required|in:TPE,PME,ETI,GE',
            'image_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'systemes_cles' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'contexte_metier' => 'nullable|string',
            'departements' => 'nullable|string',
            'actifs_critiques' => 'nullable|string',
            'chiffre_affaires' => 'nullable|string|max:50',
            'effectif' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Gestion du logo
            if ($request->hasFile('image_logo')) {
                $path = $request->file('image_logo')->store('logos', 'public');
                $validated['image_logo'] = $path;
            }
            
            // Gestion du checkbox is_active
            $validated['is_active'] = $request->has('is_active');
            
            $entreprise = Entreprise::create($validated);
            
            DB::commit();
            
            return redirect()
                ->route('entreprises.show', $entreprise->id)
                ->with('success', 'Entreprise créée avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Supprimer le logo si uploadé mais erreur
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche les détails d'une entreprise spécifique
     */
    public function show($id)
    {
        $entreprise = Entreprise::withTrashed()->findOrFail($id);
        
        // Récupérer les audits associés à cette entreprise
        $audits = Audit::where('entreprise_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Statistiques des audits
        $auditStats = [
            'total' => Audit::where('entreprise_id', $id)->count(),
            'en_cours' => Audit::where('entreprise_id', $id)->where('status', 'en_cours')->count(),
            'termine' => Audit::where('entreprise_id', $id)->where('status', 'termine')->count(),
            'moyenne_score' => Audit::where('entreprise_id', $id)->avg('score_total'),
        ];
        
        return view('pages.entreprises.show', compact('entreprise', 'audits', 'auditStats'));
    }
    
    /**
     * Affiche le formulaire d'édition d'une entreprise
     */
    public function edit($id)
    {
        $entreprise = Entreprise::findOrFail($id);
        return view('pages.entreprises.edit', compact('entreprise'));
    }
    
    /**
     * Met à jour les informations d'une entreprise existante
     */
    public function update(Request $request, $id)
    {
        $entreprise = Entreprise::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'secteur' => 'required|string|max:100',
            'taille' => 'required|in:TPE,PME,ETI,GE',
            'image_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'systemes_cles' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'contexte_metier' => 'nullable|string',
            'departements' => 'nullable|string',
            'actifs_critiques' => 'nullable|string',
            'chiffre_affaires' => 'nullable|string|max:50',
            'effectif' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'remove_logo' => 'sometimes|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Gestion de la suppression du logo
            if ($request->has('remove_logo') && $request->remove_logo == 1) {
                if ($entreprise->image_logo) {
                    Storage::disk('public')->delete($entreprise->image_logo);
                    $validated['image_logo'] = null;
                }
            }
            
            // Gestion du nouveau logo
            if ($request->hasFile('image_logo')) {
                // Supprimer l'ancien logo si existant
                if ($entreprise->image_logo) {
                    Storage::disk('public')->delete($entreprise->image_logo);
                }
                
                $path = $request->file('image_logo')->store('logos', 'public');
                $validated['image_logo'] = $path;
            }
            
            // Gestion du checkbox is_active
            $validated['is_active'] = $request->has('is_active');
            
            // Retirer remove_logo des données à mettre à jour
            unset($validated['remove_logo']);
            
            $entreprise->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('entreprises.show', $entreprise->id)
                ->with('success', 'Entreprise mise à jour avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Supprimer le nouveau logo si uploadé mais erreur
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime une entreprise (soft delete)
     */
    public function destroy($id)
    {
        $entreprise = Entreprise::findOrFail($id);
        
        // Vérifier si l'entreprise a des audits
        $auditsCount = Audit::where('entreprise_id', $id)->count();
        
        if ($auditsCount > 0) {
            return redirect()
                ->back()
                ->with('warning', 'Impossible de supprimer cette entreprise car elle possède ' . $auditsCount . ' audit(s) associé(s).');
        }
        
        try {
            // Soft delete (grâce au trait SoftDeletes dans le modèle)
            $entreprise->delete();
            
            // Optionnel: Ne pas supprimer le logo physiquement pour pouvoir le restaurer
            // Si vous voulez supprimer le logo: Storage::disk('public')->delete($entreprise->image_logo);
            
            return redirect()
                ->route('entreprises.index')
                ->with('success', 'Entreprise supprimée avec succès !');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
    
    /**
     * Restaure une entreprise supprimée (soft delete)
     */
    public function restore($id)
    {
        $entreprise = Entreprise::withTrashed()->findOrFail($id);
        
        try {
            $entreprise->restore();
            
            return redirect()
                ->route('entreprises.show', $entreprise->id)
                ->with('success', 'Entreprise restaurée avec succès !');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime définitivement une entreprise (force delete)
     */
    public function forceDestroy($id)
    {
        $entreprise = Entreprise::withTrashed()->findOrFail($id);
        
        // Vérifier si l'entreprise a des audits
        $auditsCount = Audit::where('entreprise_id', $id)->count();
        
        if ($auditsCount > 0) {
            return redirect()
                ->back()
                ->with('warning', 'Impossible de supprimer définitivement cette entreprise car elle possède ' . $auditsCount . ' audit(s) associé(s).');
        }
        
        try {
            // Supprimer le logo physiquement
            if ($entreprise->image_logo) {
                Storage::disk('public')->delete($entreprise->image_logo);
            }
            
            // Force delete
            $entreprise->forceDelete();
            
            return redirect()
                ->route('entreprises.index')
                ->with('success', 'Entreprise supprimée définitivement !');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression définitive : ' . $e->getMessage());
        }
    }
    
    /**
     * Active/Désactive une entreprise
     */
    public function toggleActive($id)
    {
        $entreprise = Entreprise::findOrFail($id);
        
        try {
            $entreprise->is_active = !$entreprise->is_active;
            $entreprise->save();
            
            $status = $entreprise->is_active ? 'activée' : 'désactivée';
            
            return redirect()
                ->back()
                ->with('success', "Entreprise {$status} avec succès !");
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors du changement de statut : ' . $e->getMessage());
        }
    }
}