<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Entreprise;
use App\Models\Reference;
use App\Models\Clause;
use App\Models\Controle;
use App\Models\Evaluation;
use App\Models\AuditReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditController extends Controller
{
    /**
     * Afficher la liste des audits
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les audits selon le rôle
        if ($user->role === 'admin' || $user->role === 'formateur') {
            $audits = Audit::with(['entreprise', 'auditeur.utilisateur', 'references'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $audits = Audit::with(['entreprise', 'auditeur.utilisateur', 'references'])
                ->where('auditeur_id', $user->auditeur->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        // Compter les audits par statut
        $stats = [
            'total' => Audit::count(),
            'en_cours' => Audit::where('status', 'en_cours')->count(),
            'termine' => Audit::where('status', 'termine')->count(),
            'suspendu' => Audit::where('status', 'suspendu')->count(),
            'annule' => Audit::where('status', 'annule')->count(),
        ];
        
        return view('pages.audits.index', [
            'title' => 'Mes audits',
            'audits' => $audits,
            'stats' => $stats
        ]);
    }
    
    /**
     * Afficher le formulaire de création d'audit
     */
    public function create()
    {
        $entreprises = Entreprise::where('is_active', true)->orderBy('nom')->get();
        $references = Reference::where('is_active', true)->orderBy('code')->get();
        
        return view('pages.audits.create', [
            'title' => 'Nouvel audit',
            'entreprises' => $entreprises,
            'references' => $references
        ]);
    }
    
    /**
     * Enregistrer un nouvel audit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'reference_ids' => 'required|array|min:1',
            'reference_ids.*' => 'exists:references,id',
        ]);
        
        $user = Auth::user();
        
        // Créer l'audit
        $audit = Audit::create([
            'id' => Str::uuid(),
            'auditeur_id' => $user->auditeur->id,
            'entreprise_id' => $validated['entreprise_id'],
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'status' => 'en_cours',
            'score_total' => 0,
            'temps_total' => 0,
        ]);
        
        // Associer les référentiels
        foreach ($validated['reference_ids'] as $referenceId) {
            AuditReference::create([
                'id' => Str::uuid(),
                'audit_id' => $audit->id,
                'reference_id' => $referenceId,
                'objectif_specifique' => null,
                'ponderation' => 1,
            ]);
        }
        
        // Récupérer les références
        $iso27001Ref = Reference::where('code', 'ISO27001')->first();
        $iso27002Ref = Reference::where('code', 'ISO27002')->first();
        $iso27005Ref = Reference::where('code', 'ISO27005')->first();
        
        $selectedRefIds = $validated['reference_ids'];
        
        // 1. Créer TOUTES les clauses ISO 27001 (niveau 1) si sélectionné
        if ($iso27001Ref && in_array($iso27001Ref->id, $selectedRefIds)) {
            $clauses = Clause::where('reference_id', $iso27001Ref->id)
                ->where('niveau', 1)
                ->whereBetween('numero', ['4', '10'])
                ->orderBy('numero')
                ->get();
            
            foreach ($clauses as $clause) {
                AuditClauseEvaluation::create([
                    'id' => Str::uuid(),
                    'audit_id' => $audit->id,
                    'clause_id' => $clause->id,
                    'statut' => 'non_audite',
                    'justification' => null,
                    'recommandation' => null,
                    'niveau_conformite' => null,
                    'score' => 0,
                ]);
            }
            
            $clauseCount = $clauses->count();
        }
        
        // 2. Créer TOUS les contrôles ISO 27002 si sélectionné
        if ($iso27002Ref && in_array($iso27002Ref->id, $selectedRefIds)) {
            $controls = Controle::whereHas('clause.reference', function($query) use ($iso27002Ref) {
                $query->where('id', $iso27002Ref->id);
            })->get();
            
            foreach ($controls as $control) {
                Evaluation::create([
                    'id' => Str::uuid(),
                    'audit_id' => $audit->id,
                    'controle_id' => $control->id,
                    'statut' => 'non_audite',
                    'justification' => null,
                    'recommandation' => null,
                    'gravite_risque' => null,
                    'probabilite_risque' => null,
                    'niveau_risque_calcule' => null,
                    'preuves_consultees' => json_encode([]),
                    'commentaire_formateur' => null,
                    'date_evaluation' => now(),
                    'points_obtenus' => 0,
                ]);
            }
            
            $controlCount = $controls->count();
        }
        
        // 3. Créer TOUTES les méthodes d'analyse de risques ISO 27005 si sélectionné
        if ($iso27005Ref && in_array($iso27005Ref->id, $selectedRefIds)) {
            $risks = Controle::whereHas('clause.reference', function($query) use ($iso27005Ref) {
                $query->where('id', $iso27005Ref->id);
            })->get();
            
            foreach ($risks as $risk) {
                Evaluation::create([
                    'id' => Str::uuid(),
                    'audit_id' => $audit->id,
                    'controle_id' => $risk->id,
                    'statut' => 'non_audite',
                    'justification' => null,
                    'recommandation' => null,
                    'gravite_risque' => null,
                    'probabilite_risque' => null,
                    'niveau_risque_calcule' => null,
                    'preuves_consultees' => json_encode([]),
                    'commentaire_formateur' => null,
                    'date_evaluation' => now(),
                    'points_obtenus' => 0,
                ]);
            }
            
            $riskCount = $risks->count();
        }
        
        $message = 'Audit créé avec succès ! ';
        if (isset($clauseCount)) $message .= $clauseCount . ' clauses ISO 27001, ';
        if (isset($controlCount)) $message .= $controlCount . ' contrôles ISO 27002, ';
        if (isset($riskCount)) $message .= $riskCount . ' méthodes ISO 27005';
        $message = rtrim($message, ', ');
        
        return redirect()->route('audits.show', $audit->id)
            ->with('success', $message);
    }
        
    /**
     * Afficher les détails d'un audit
     */
    public function show($id)
    {
        $audit = Audit::with([
            'entreprise',
            'auditeur.utilisateur',
            'references',
            'evaluations' => function($query) {
                $query->with('controle');
            },
            'clauseEvaluations' => function($query) {
                $query->with('clause')->orderBy('clause_id');
            }
        ])->findOrFail($id);
        
        // Calculer la progression des contrôles
        $totalControls = $audit->evaluations->count();
        $evaluatedControls = $audit->evaluations->where('statut', '!=', 'non_audite')->count();
        $progress = $totalControls > 0 ? round(($evaluatedControls / $totalControls) * 100) : 0;
        
        // Calculer le taux de conformité des contrôles
        $conformControls = $audit->evaluations->where('statut', 'conforme')->count();
        $conformityRate = $audit->evaluations->count() > 0 
            ? round(($conformControls / $audit->evaluations->count()) * 100) 
            : 0;
        
        // Calculer la progression des clauses
        $totalClauses = $audit->clauseEvaluations->count();
        $evaluatedClauses = $audit->clauseEvaluations->where('statut', '!=', 'non_audite')->count();
        $clauseProgress = $totalClauses > 0 ? round(($evaluatedClauses / $totalClauses) * 100) : 0;
        
        // Statistiques par statut pour les contrôles
        $statsByStatus = [
            'non_audite' => $audit->evaluations->where('statut', 'non_audite')->count(),
            'conforme' => $conformControls,
            'non_conforme' => $audit->evaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->evaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->evaluations->where('statut', 'non_applicable')->count(),
        ];
        
        return view('pages.audits.show', [
            'title' => $audit->titre,
            'audit' => $audit,
            'progress' => $progress,
            'conformityRate' => $conformityRate,
            'clauseProgress' => $clauseProgress,
            'statsByStatus' => $statsByStatus
        ]);
    }
        
    /**
     * Afficher l'audit en cours
     */
    public function current()
    {
        $user = Auth::user();
        
        // Récupérer l'audit en cours pour cet auditeur
        $audit = Audit::with([
            'entreprise',
            'auditeur.utilisateur',
            'references',
            'evaluations' => function($query) {
                $query->with('controle');
            }
        ])
        ->where('auditeur_id', $user->auditeur->id)
        ->where('status', 'en_cours')
        ->latest()
        ->first();
        
        // Si aucun audit en cours, rediriger vers la liste des audits
        if (!$audit) {
            return redirect()->route('audits.index')
                ->with('info', 'Aucun audit en cours. Créez un nouvel audit pour commencer.');
        }
        
        // Calculer la progression
        $totalControls = $audit->evaluations->count();
        $evaluatedControls = $audit->evaluations->where('statut', '!=', 'non_audite')->count();
        $progress = $totalControls > 0 ? round(($evaluatedControls / $totalControls) * 100) : 0;
        
        // Calculer le taux de conformité
        $conformControls = $audit->evaluations->where('statut', 'conforme')->count();
        $conformityRate = $audit->evaluations->count() > 0 
            ? round(($conformControls / $audit->evaluations->count()) * 100) 
            : 0;
        
        // Statistiques par statut pour le dashboard
        $statsByStatus = [
            'non_audite' => $audit->evaluations->where('statut', 'non_audite')->count(),
            'conforme' => $conformControls,
            'non_conforme' => $audit->evaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->evaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->evaluations->where('statut', 'non_applicable')->count(),
        ];
        
        // Récupérer les contrôles par catégorie pour l'affichage
        $controlsByCategory = $audit->evaluations->groupBy(function($evaluation) {
            return $evaluation->controle->categorie ?? 'Autres';
        });
        
        // Récupérer les dernières preuves consultées
        $recentEvidences = collect();
        foreach ($audit->evaluations as $evaluation) {
            $preuves = json_decode($evaluation->preuves_consultees, true);
            if (!empty($preuves)) {
                foreach ($preuves as $preuve) {
                    $recentEvidences->push([
                        'titre' => $preuve,
                        'date' => $evaluation->updated_at,
                        'controle' => $evaluation->controle->code
                    ]);
                }
            }
        }
        $recentEvidences = $recentEvidences->sortByDesc('date')->take(5);
        
        return view('pages.audits.current', [
            'title' => 'Audit en cours',
            'audit' => $audit,
            'progress' => $progress,
            'conformityRate' => $conformityRate,
            'statsByStatus' => $statsByStatus,
            'controlsByCategory' => $controlsByCategory,
            'recentEvidences' => $recentEvidences
        ]);
    }
    
    /**
     * Afficher la collecte de preuves
     */
    public function evidence()
    {
        $user = Auth::user();
        
        // Récupérer l'audit en cours
        $audit = Audit::where('auditeur_id', $user->auditeur->id)
            ->where('status', 'en_cours')
            ->latest()
            ->first();
        
        if (!$audit) {
            return redirect()->route('audits.index')
                ->with('info', 'Aucun audit en cours.');
        }
        
        // Récupérer les preuves par contrôle
        $preuves = \App\Models\Preuve::with('controle')
            ->orderBy('controle_id')
            ->orderBy('ordre_affichage')
            ->get()
            ->groupBy('controle.code');
        
        return view('pages.audits.evidence', [
            'title' => 'Collecte de preuves',
            'audit' => $audit,
            'preuves' => $preuves
        ]);
    }
    
    /**
     * Afficher l'évaluation de conformité
     */
    public function evaluation()
    {
        $user = Auth::user();
        
        // Récupérer l'audit en cours
        $audit = Audit::with(['evaluations.controle', 'evaluations.controle.preuves'])
            ->where('auditeur_id', $user->auditeur->id)
            ->where('status', 'en_cours')
            ->latest()
            ->first();
        
        if (!$audit) {
            return redirect()->route('audits.index')
                ->with('info', 'Aucun audit en cours.');
        }
        
        return view('pages.audits.evaluation', [
            'title' => 'Évaluation de conformité',
            'audit' => $audit,
            'evaluations' => $audit->evaluations
        ]);
    }
    
    /**
     * Mettre à jour l'évaluation d'un contrôle
     */
    public function updateEvaluation(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $validated = $request->validate([
            'statut' => 'required|in:non_audite,conforme,non_conforme,partiellement_conforme,non_applicable',
            'justification' => 'nullable|string',
            'recommandation' => 'nullable|string',
            'gravite_risque' => 'nullable|in:faible,moyen,eleve,critique',
            'probabilite_risque' => 'nullable|in:faible,moyen,eleve',
            'points_obtenus' => 'nullable|numeric|min:0',
        ]);
        
        // Calculer le niveau de risque
        $niveauRisque = null;
        if ($validated['gravite_risque'] && $validated['probabilite_risque']) {
            $niveauRisque = $this->calculateRiskLevel(
                $validated['gravite_risque'],
                $validated['probabilite_risque']
            );
        }
        
        $evaluation->update([
            'statut' => $validated['statut'],
            'justification' => $validated['justification'],
            'recommandation' => $validated['recommandation'],
            'gravite_risque' => $validated['gravite_risque'] ?? null,
            'probabilite_risque' => $validated['probabilite_risque'] ?? null,
            'niveau_risque_calcule' => $niveauRisque,
            'points_obtenus' => $validated['points_obtenus'] ?? 0,
            'date_evaluation' => now(),
        ]);
        
        // Mettre à jour le score total de l'audit
        $this->updateAuditScore($evaluation->audit_id);
        
        return redirect()->back()->with('success', 'Évaluation mise à jour avec succès !');
    }
    
    /**
     * Calculer le niveau de risque
     */
    private function calculateRiskLevel($gravite, $probabilite)
    {
        $levels = [
            'faible' => 1,
            'moyen' => 2,
            'eleve' => 3,
            'critique' => 4
        ];
        
        $score = ($levels[$gravite] ?? 1) * ($levels[$probabilite] ?? 1);
        
        if ($score <= 4) return 'faible';
        if ($score <= 8) return 'moyen';
        if ($score <= 12) return 'eleve';
        return 'critique';
    }
    
    /**
     * Mettre à jour le score total de l'audit
     */
    private function updateAuditScore($auditId)
    {
        $audit = Audit::find($auditId);
        if (!$audit) return;
        
        $totalPoints = 0;
        $maxPoints = 0;
        
        foreach ($audit->evaluations as $evaluation) {
            $controle = $evaluation->controle;
            $ponderation = $controle->ponderation_defaut ?? 1;
            $maxPoints += $ponderation;
            
            if ($evaluation->statut === 'conforme') {
                $totalPoints += $ponderation;
            } elseif ($evaluation->statut === 'partiellement_conforme') {
                $totalPoints += $ponderation * 0.5;
            }
        }
        
        $score = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100) : 0;
        $audit->update(['score_total' => $score]);
    }
    
    /**
     * Afficher la gestion des risques
     */
    public function risk()
    {
        $user = Auth::user();
        
        // Récupérer l'audit en cours
        $audit = Audit::with(['evaluations' => function($query) {
            $query->where('statut', 'non_conforme')
                  ->orWhere('statut', 'partiellement_conforme')
                  ->with('controle');
        }])->where('auditeur_id', $user->auditeur->id)
            ->where('status', 'en_cours')
            ->latest()
            ->first();
        
        if (!$audit) {
            return redirect()->route('audits.index')
                ->with('info', 'Aucun audit en cours.');
        }
        
        $risks = $audit->evaluations->filter(function($evaluation) {
            return in_array($evaluation->statut, ['non_conforme', 'partiellement_conforme']);
        });
        
        return view('pages.audits.risk', [
            'title' => 'Gestion des risques',
            'audit' => $audit,
            'risks' => $risks
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $audit = Audit::with(['references'])->findOrFail($id);
        $entreprises = Entreprise::where('is_active', true)->orderBy('nom')->get();
        $references = Reference::where('is_active', true)->orderBy('code')->get();
        
        // Récupérer les IDs des contrôles déjà associés via evaluations
        $selectedControlIds = Evaluation::where('audit_id', $id)
            ->pluck('controle_id')
            ->toArray();
        
        return view('pages.audits.edit', [
            'title' => 'Modifier l\'audit',
            'audit' => $audit,
            'entreprises' => $entreprises,
            'references' => $references,
            'selectedControlIds' => $selectedControlIds
        ]);
    }
    
    /**
     * Mettre à jour un audit
     */
    public function update(Request $request, $id)
    {
        $audit = Audit::findOrFail($id);
        
        $validated = $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'status' => 'required|in:en_cours,suspendu,termine,annule',
            'reference_ids' => 'required|array|min:1',
            'reference_ids.*' => 'exists:references,id',
            'control_ids' => 'nullable|array',
            'control_ids.*' => 'exists:controles,id',
        ]);
        
        $audit->update([
            'entreprise_id' => $validated['entreprise_id'],
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'status' => $validated['status'],
        ]);
        
        // Mettre à jour les référentiels associés
        AuditReference::where('audit_id', $audit->id)->delete();
        foreach ($validated['reference_ids'] as $referenceId) {
            AuditReference::create([
                'id' => Str::uuid(),
                'audit_id' => $audit->id,
                'reference_id' => $referenceId,
                'objectif_specifique' => null,
                'ponderation' => 1,
            ]);
        }
        
        // Mettre à jour les contrôles associés dans evaluations
        if (!empty($validated['control_ids'])) {
            // Supprimer les anciens contrôles
            Evaluation::where('audit_id', $audit->id)->delete();
            
            // Ajouter les nouveaux contrôles
            foreach ($validated['control_ids'] as $controlId) {
                Evaluation::create([
                    'id' => Str::uuid(),
                    'audit_id' => $audit->id,
                    'controle_id' => $controlId,
                    'statut' => 'non_audite',
                    'justification' => null,
                    'recommandation' => null,
                    'gravite_risque' => null,
                    'probabilite_risque' => null,
                    'niveau_risque_calcule' => null,
                    'preuves_consultees' => json_encode([]),
                    'commentaire_formateur' => null,
                    'date_evaluation' => now(),
                    'points_obtenus' => 0,
                ]);
            }
        }
        
        return redirect()->route('audits.show', $audit->id)
            ->with('success', 'Audit mis à jour avec succès !');
    }
    
    /**
     * Supprimer un audit
     */
    public function destroy($id)
    {
        $audit = Audit::findOrFail($id);
        $audit->delete();
        
        return redirect()->route('audits.index')
            ->with('success', 'Audit supprimé avec succès !');
    }
    
    /**
     * Démarrer un audit
     */
    public function start($id)
    {
        $audit = Audit::findOrFail($id);
        
        if ($audit->status !== 'en_cours') {
            $audit->update(['status' => 'en_cours']);
        }
        
        return redirect()->route('audit.current')
            ->with('success', 'Audit démarré !');
    }

    /**
     * Récupérer les données d'une entreprise (API)
     */
    public function getEnterpriseData($id)
    {
        $entreprise = Entreprise::findOrFail($id);
        return response()->json($entreprise);
    }

    /**
     * Récupérer la structure complète d'une référence ISO (API)
     */
    public function getReferenceStructure($id)
    {
        $reference = Reference::with(['clauses' => function($query) {
            $query->whereNull('parent_id')->orderBy('ordre_affichage');
        }, 'clauses.children'])->findOrFail($id);
        
        $response = [
            'id' => $reference->id,
            'code' => $reference->code,
            'type' => $reference->code,
            'nom' => $reference->nom,
        ];
        
        if ($reference->code === 'ISO27001') {
            // Récupérer toutes les clauses avec leurs sous-clauses
            $clauses = Clause::where('reference_id', $reference->id)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->orderBy('numero');
                }])
                ->orderBy('numero')
                ->get();
            
            $response['clauses'] = $clauses->map(function($clause) {
                return [
                    'id' => $clause->id,
                    'numero' => $clause->numero,
                    'titre' => $clause->titre,
                    'description' => $clause->description,
                    'niveau_importance' => $clause->niveau_importance,
                    'children' => $clause->children->map(function($child) {
                        return [
                            'id' => $child->id,
                            'numero' => $child->numero,
                            'titre' => $child->titre,
                            'description' => $child->description,
                        ];
                    })
                ];
            });
        }
        elseif ($reference->code === 'ISO27002') {
            // Récupérer tous les contrôles ISO 27002 avec leurs preuves
            $controls = Controle::with(['clause', 'preuves'])
                ->orderBy('code')
                ->get();
            
            $response['controls'] = $controls->map(function($control) {
                return [
                    'id' => $control->id,
                    'code' => $control->code,
                    'titre' => $control->titre,
                    'description' => $control->description,
                    'objectif' => $control->objectif,
                    'categorie' => $control->categorie,
                    'sous_categorie' => $control->sous_categorie,
                    'niveau_critique' => $control->niveau_critique,
                    'type_controle' => $control->type_controle,
                    'ponderation_defaut' => $control->ponderation_defaut,
                    'temps_estime' => $control->temps_estime,
                    'conseils_audit' => $control->conseils_audit,
                    'preuves' => $control->preuves->map(function($preuve) {
                        return [
                            'id' => $preuve->id,
                            'titre' => $preuve->titre,
                            'type_preuve' => $preuve->type_preuve,
                            'contenu' => $preuve->contenu,
                            'points_max' => $preuve->points_max,
                        ];
                    }),
                    'clause' => $control->clause ? [
                        'id' => $control->clause->id,
                        'numero' => $control->clause->numero,
                        'titre' => $control->clause->titre
                    ] : null
                ];
            });
        }
        elseif ($reference->code === 'ISO27005') {
            // Récupérer les méthodes d'analyse de risques
            $risks = Controle::whereHas('clause', function($query) use ($reference) {
                $query->where('reference_id', $reference->id);
            })->with(['preuves'])->orderBy('code')->get();
            
            $response['risks'] = $risks->map(function($risk) {
                return [
                    'id' => $risk->id,
                    'code' => $risk->code,
                    'titre' => $risk->titre,
                    'description' => $risk->description,
                    'objectif' => $risk->objectif,
                    'categorie' => $risk->categorie,
                    'type_controle' => $risk->type_controle,
                    'preuves' => $risk->preuves->map(function($preuve) {
                        return [
                            'id' => $preuve->id,
                            'titre' => $preuve->titre,
                            'type_preuve' => $preuve->type_preuve,
                            'contenu' => $preuve->contenu,
                        ];
                    }),
                ];
            });
        }
        
        return response()->json($response);
    }

    /**
 * Afficher les contrôles ISO 27002
 */
public function controls($section = null)
{
    $query = Controle::with('clause');
    
    if ($section) {
        $query->where('categorie', 'LIKE', '%' . $section . '%')
              ->orWhere('code', 'LIKE', $section . '%');
    }
    
    $controls = $query->orderBy('code')->paginate(15);
    
    // Récupérer les catégories pour le filtre
    $categories = Controle::select('categorie')
        ->distinct()
        ->whereNotNull('categorie')
        ->pluck('categorie');
    
    // Statistiques des contrôles
    $stats = [
        'total' => Controle::count(),
        'critique' => Controle::where('niveau_critique', 'critique')->count(),
        'eleve' => Controle::where('niveau_critique', 'eleve')->count(),
        'moyen' => Controle::where('niveau_critique', 'moyen')->count(),
        'faible' => Controle::where('niveau_critique', 'faible')->count(),
    ];
    
    return view('pages.audits.controls', [
        'title' => "Contrôles ISO 27002" . ($section ? " - $section" : ""),
        'controls' => $controls,
        'categories' => $categories,
        'currentSection' => $section,
        'stats' => $stats
    ]);
}

/**
 * Mettre à jour l'évaluation d'une clause
 */
public function updateClauseEvaluation(Request $request, $id)
{
    $clauseEval = AuditClauseEvaluation::findOrFail($id);
    
    $validated = $request->validate([
        'statut' => 'required|in:non_audite,conforme,non_conforme,partiellement_conforme,non_applicable',
        'justification' => 'nullable|string',
        'recommandation' => 'nullable|string',
        'niveau_conformite' => 'nullable|in:faible,moyen,eleve,excellent',
        'score' => 'nullable|numeric|min:0|max:100',
    ]);
    
    $clauseEval->update([
        'statut' => $validated['statut'],
        'justification' => $validated['justification'],
        'recommandation' => $validated['recommandation'],
        'niveau_conformite' => $validated['niveau_conformite'],
        'score' => $validated['score'] ?? 0,
    ]);
    
    return redirect()->back()->with('success', 'Évaluation de la clause mise à jour avec succès !');
}


}