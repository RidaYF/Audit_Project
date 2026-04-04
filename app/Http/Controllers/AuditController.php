<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Entreprise;
use App\Models\Reference;
use App\Models\Clause;
use App\Models\Controle;
use App\Models\Evaluation;
use App\Models\AuditReference;
use App\Models\AuditClauseEvaluation; // Ajout important
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
        $entreprises = Entreprise::where('is_active', true)
            ->orderBy('nom')
            ->get();
        
        $references = Reference::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        // Récupérer les statistiques pour information
        $stats = [
            'iso27001_clauses' => Clause::whereHas('reference', function($q) {
                $q->where('code', 'ISO27001');
            })->where('niveau', 1)->count(),
            'iso27002_controls' => Controle::whereHas('clause', function($q) {
                $q->whereHas('reference', function($r) {
                    $r->where('code', 'ISO27002');
                });
            })->count(),
            'iso27005_methods' => Controle::whereHas('clause', function($q) {
                $q->whereHas('reference', function($r) {
                    $r->where('code', 'ISO27005');
                });
            })->count(),
        ];
        
        return view('pages.audits.create', [
            'title' => 'Nouvel audit',
            'entreprises' => $entreprises,
            'references' => $references,
            'stats' => $stats
        ]);
    }

    /**
     * Enregistrer un nouvel audit avec initialisation complète
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
            'reference_ids.*' => 'exists:iso_references,id',
        ]);
        
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a un auditeur
        if (!$user->auditeur) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas de profil auditeur.')
                ->withInput();
        }
        
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
        
        $selectedRefIds = $validated['reference_ids'];
        
        // Récupérer les IDs des références
        $iso27001Id = Reference::where('code', 'ISO27001')->value('id');
        $iso27002Id = Reference::where('code', 'ISO27002')->value('id');
        $iso27005Id = Reference::where('code', 'ISO27005')->value('id');
        
        $clauseCount = 0;
        $controlCount = 0;
        $riskCount = 0;
        
        // Démarrer une transaction
        \DB::beginTransaction();
        
        try {
            // Associer les référentiels
            foreach ($selectedRefIds as $referenceId) {
                AuditReference::create([
                    'id' => Str::uuid(),
                    'audit_id' => $audit->id,
                    'reference_id' => $referenceId,
                    'objectif_specifique' => null,
                    'ponderation' => 1,
                ]);
            }
            
            // 1. Créer les clauses ISO 27001
            if ($iso27001Id && in_array($iso27001Id, $selectedRefIds)) {
                $clauses = Clause::where('reference_id', $iso27001Id)
                    ->where('niveau', 1)
                    ->where(function($query) {
                        $query->where('numero', '4')
                            ->orWhere('numero', '5')
                            ->orWhere('numero', '6')
                            ->orWhere('numero', '7')
                            ->orWhere('numero', '8')
                            ->orWhere('numero', '9')
                            ->orWhere('numero', '10');
                    })
                    ->orderBy('ordre_affichage')
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
                    $clauseCount++;
                }
            }
            
            // 2. Créer les contrôles ISO 27002
            if ($iso27002Id && in_array($iso27002Id, $selectedRefIds)) {
                // Récupérer les IDs des clauses de la référence ISO27002
                $clauseIds = Clause::where('reference_id', $iso27002Id)->pluck('id')->toArray();
                
                // Puis récupérer les contrôles liés à ces clauses
                $controls = Controle::whereIn('clause_id', $clauseIds)->get();
                
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
                    $controlCount++;
                }
            }
            
            // 3. Créer les méthodes ISO 27005
            if ($iso27005Id && in_array($iso27005Id, $selectedRefIds)) {
                // Récupérer les IDs des clauses de la référence ISO27005
                $clauseIds = Clause::where('reference_id', $iso27005Id)->pluck('id')->toArray();
                
                // Puis récupérer les contrôles liés à ces clauses
                $risks = Controle::whereIn('clause_id', $clauseIds)->get();
                
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
                    $riskCount++;
                }
            }
            
            \DB::commit();
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur création audit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }

        // Pour la calcule automatique des indicateurs
        $this->updateAuditIndicators($audit->id);

        
        $message = 'Audit créé avec succès ! ';
        if ($clauseCount > 0) $message .= $clauseCount . ' clauses ISO 27001, ';
        if ($controlCount > 0) $message .= $controlCount . ' contrôles ISO 27002, ';
        if ($riskCount > 0) $message .= $riskCount . ' méthodes ISO 27005';
        $message = rtrim($message, ', ');
        
        return redirect()->route('audits.show', $audit->id)
            ->with('success', $message);
    }
            
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
                $query->with('clause');
            }
        ])->findOrFail($id);
        
        // Vérifier les droits d'accès
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'formateur' && $audit->auditeur_id !== $user->auditeur->id) {
            abort(403, 'Vous n\'avez pas accès à cet audit.');
        }
        
        // Récupérer les indicateurs globaux
        $progress = $audit->progress_rate ?? 0;
        $conformityRate = $audit->conformity_rate ?? 0;
        
        // Statistiques des contrôles
        $totalControls = $audit->evaluations->count();
        $evaluatedControls = $audit->evaluations->where('statut', '!=', 'non_audite')->count();
        $conformControls = $audit->evaluations->where('statut', 'conforme')->count();
        
        // Statistiques des clauses
        $totalClauses = $audit->clauseEvaluations->count();
        $evaluatedClauses = $audit->clauseEvaluations->where('statut', '!=', 'non_audite')->count();
        $conformClauses = $audit->clauseEvaluations->where('statut', 'conforme')->count();
        $clauseProgress = $totalClauses > 0 ? round(($evaluatedClauses / $totalClauses) * 100) : 0;
        
        // Statistiques par statut pour les contrôles
        $statsByStatus = [
            'non_audite' => $audit->evaluations->where('statut', 'non_audite')->count(),
            'conforme' => $conformControls,
            'non_conforme' => $audit->evaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->evaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->evaluations->where('statut', 'non_applicable')->count(),
        ];
        
        // Statistiques par statut pour les clauses
        $clauseStatsByStatus = [
            'non_audite' => $audit->clauseEvaluations->where('statut', 'non_audite')->count(),
            'conforme' => $conformClauses,
            'non_conforme' => $audit->clauseEvaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->clauseEvaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->clauseEvaluations->where('statut', 'non_applicable')->count(),
        ];
        
        // Récupérer les contrôles par catégorie
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
        
        return view('pages.audits.show', [
            'title' => $audit->titre,
            'audit' => $audit,
            'progress' => $progress,
            'conformityRate' => $conformityRate,
            'clauseProgress' => $clauseProgress,
            'statsByStatus' => $statsByStatus,
            'clauseStatsByStatus' => $clauseStatsByStatus,
            'controlsByCategory' => $controlsByCategory,
            'recentEvidences' => $recentEvidences,
            // Indicateurs supplémentaires
            'totalControls' => $totalControls,
            'evaluatedControls' => $evaluatedControls,
            'totalClauses' => $totalClauses,
            'evaluatedClauses' => $evaluatedClauses,
        ]);
    }
        
    /**
     * Afficher l'audit en cours
     */
    public function current($specificAuditId = null)
    {
        $user = Auth::user();
        
        // Si un ID spécifique est passé dans l'URL, l'utiliser
        if ($specificAuditId) {
            $audit = Audit::with([
                'entreprise',
                'auditeur.utilisateur',
                'references',
                'evaluations' => function($query) {
                    $query->with('controle');
                },
                'clauseEvaluations' => function($query) {
                    $query->with('clause');
                }
            ])->find($specificAuditId);
            
            if ($audit && $audit->status === 'en_cours') {
                // Vérifier les droits d'accès
                if ($user->role === 'admin' || $user->role === 'formateur' || $audit->auditeur_id === $user->auditeur->id) {
                    session(['current_audit_id' => $audit->id]);
                    return $this->renderCurrentAudit($audit);
                } else {
                    return redirect()->route('audits.index')
                        ->with('error', 'Vous n\'avez pas accès à cet audit.');
                }
            } else {
                return redirect()->route('audits.index')
                    ->with('error', 'Audit non trouvé ou non commencé.');
            }
        }
        
        // Sinon, vérifier si un audit est défini en session
        $auditId = session('current_audit_id');
        
        if ($auditId) {
            $audit = Audit::with([
                'entreprise',
                'auditeur.utilisateur',
                'references',
                'evaluations' => function($query) {
                    $query->with('controle');
                },
                'clauseEvaluations' => function($query) {
                    $query->with('clause');
                }
            ])->find($auditId);
            
            if ($audit && $audit->status === 'en_cours') {
                return $this->renderCurrentAudit($audit);
            }
        }
        
        // Sinon, récupérer le premier audit en cours
        $audit = Audit::with([
            'entreprise',
            'auditeur.utilisateur',
            'references',
            'evaluations' => function($query) {
                $query->with('controle');
            },
            'clauseEvaluations' => function($query) {
                $query->with('clause');
            }
        ])
        ->where('auditeur_id', $user->auditeur->id)
        ->where('status', 'en_cours')
        ->latest()
        ->first();
        
        if (!$audit) {
            return redirect()->route('audits.index')
                ->with('info', 'Aucun audit en cours. Créez un nouvel audit pour commencer.');
        }
        
        // Définir l'audit dans la session
        session(['current_audit_id' => $audit->id]);
        
        return $this->renderCurrentAudit($audit);
    }

    /**
     * Rendre la vue de l'audit en cours
     */
    private function renderCurrentAudit($audit)
    {
        // Utiliser les indicateurs déjà calculés et sauvegardés
        $progress = $audit->progress_rate ?? 0;
        $conformityRate = $audit->conformity_rate ?? 0;
        
        // Récupérer les contrôles par catégorie
        $controlsByCategory = $audit->evaluations->groupBy(function($evaluation) {
            return $evaluation->controle->categorie ?? 'Autres';
        });
        
        // Récupérer les méthodes de risques ISO 27005
        $riskMethods = $audit->evaluations->filter(function($evaluation) {
            return $evaluation->controle->code && str_starts_with($evaluation->controle->code, 'RM-');
        });
        
        // Récupérer les dernières preuves
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
        
        // Calculer le nombre de contrôles évalués
        $totalControls = $audit->evaluations->count();
        $evaluatedControls = $audit->evaluations->where('statut', '!=', 'non_audite')->count();
        
        return view('pages.audits.current', [
            'title' => 'Audit en cours',
            'audit' => $audit,
            'progress' => $progress,
            'conformityRate' => $conformityRate,
            'totalControls' => $totalControls,
            'evaluatedControls' => $evaluatedControls,
            'controlsByCategory' => $controlsByCategory,
            'riskMethods' => $riskMethods,
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
 * Afficher l'évaluation de conformité (liste des contrôles et clauses)
 */
public function evaluation(Request $request)
{
    $user = Auth::user();
    
    // Récupérer l'audit en cours
    $auditId = session('current_audit_id');
    
    if (!$auditId) {
        return redirect()->route('audit.current')->with('error', 'Veuillez sélectionner un audit d\'abord.');
    }
    
    $audit = Audit::with('entreprise')->findOrFail($auditId);
    
    // Récupérer le contrôle spécifique si passé en paramètre
    $controleId = $request->get('controle');
    
    if ($controleId) {
        // Évaluation d'un contrôle spécifique
        $control = Controle::findOrFail($controleId);
        
        $evaluation = Evaluation::firstOrCreate(
            [
                'audit_id' => $audit->id,
                'controle_id' => $control->id
            ],
            [
                'id' => Str::uuid(),
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
            ]
        );
        
        return view('pages.audits.evaluation-single', [
            'title' => 'Évaluation du contrôle - ' . $control->code,
            'audit' => $audit,
            'evaluation' => $evaluation,
            'control' => $control
        ]);
    } else {
        // Liste de tous les contrôles ET clauses
        $evaluations = Evaluation::with('controle')
            ->where('audit_id', $audit->id)
            ->orderBy('created_at')
            ->get();
        
        $clauseEvaluations = AuditClauseEvaluation::with('clause')
            ->where('audit_id', $audit->id)
            ->orderBy('created_at')
            ->get();
        
        return view('pages.audits.evaluation', [
            'title' => 'Évaluation de conformité',
            'audit' => $audit,
            'evaluations' => $evaluations,
            'clauseEvaluations' => $clauseEvaluations
        ]);
    }
}
    
/**
 * Afficher l'évaluation d'un contrôle spécifique (création ou modification)
 */
public function evaluationSingle($controleId)
{
    $user = Auth::user();
    
    // Récupérer l'audit en cours
    $auditId = session('current_audit_id');
    
    if (!$auditId) {
        return redirect()->route('audit.current')->with('error', 'Veuillez sélectionner un audit d\'abord.');
    }
    
    $audit = Audit::with('entreprise')->findOrFail($auditId);
    
    // Récupérer le contrôle
    $control = Controle::findOrFail($controleId);
    
    // Vérifier si une évaluation existe déjà pour ce contrôle dans cet audit
    $evaluation = Evaluation::firstOrCreate(
        [
            'audit_id' => $audit->id,
            'controle_id' => $control->id
        ],
        [
            'id' => Str::uuid(),
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
        ]
    );
    
    return view('pages.audits.evaluation-single', [
        'title' => 'Évaluation du contrôle - ' . $control->code,
        'audit' => $audit,
        'evaluation' => $evaluation,
        'control' => $control
    ]);
}

    /**
     * Changer l'audit en cours
     */
    public function switchAudit($id)
    {
        $audit = Audit::findOrFail($id);
        
        // Vérifier les droits
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'formateur' && $audit->auditeur_id !== $user->auditeur->id) {
            abort(403);
        }
        
        // Mettre à jour la session
        session(['current_audit_id' => $audit->id]);
        
        return redirect()->route('audit.current')
            ->with('success', 'Audit changé : ' . $audit->titre);
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
        
        // Mettre à jour tous les indicateurs de l'audit
        $this->updateAuditIndicators($evaluation->audit_id);
        
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
     * Afficher la gestion des risques (non-conformités)
     */
    /**
 * Afficher la gestion des risques (non-conformités des clauses)
 */
public function risk()
{
    // Récupérer l'audit en cours depuis la session
    $auditId = session('current_audit_id');
    
    if (!$auditId) {
        return redirect()->route('audit.current')->with('error', 'Veuillez sélectionner un audit d\'abord.');
    }
    
    $audit = Audit::with('entreprise')->findOrFail($auditId);
    
    // Récupérer UNIQUEMENT les non-conformités des CLAUSES
    $nonConformites = AuditClauseEvaluation::with(['clause', 'clause.reference'])
        ->where('audit_id', $audit->id)
        ->where('statut', 'non_conforme')  // Filtrer sur les clauses non conformes
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Statistiques
    $stats = [
        'total' => $nonConformites->count(),
        'par_reference' => $nonConformites->groupBy(function($item) {
            return $item->clause->reference->code ?? 'Inconnu';
        })->map(function($group) {
            return $group->count();
        })->toArray()
    ];
    
    return view('pages.audits.risk', [
        'title' => 'Gestion des risques - Non-conformités des clauses',
        'audit' => $audit,
        'nonConformites' => $nonConformites,
        'stats' => $stats
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

        // Indicateurs calcul
        $this->updateAuditIndicators($id);

        
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

        $this->updateAuditIndicators($id);
        
        // Définir l'audit en cours dans la session
        session(['current_audit_id' => $audit->id]);
        
        
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
        
        $categories = Controle::select('categorie')
            ->distinct()
            ->whereNotNull('categorie')
            ->pluck('categorie');
        
        $stats = [
            'total' => Controle::count(),
            'critique' => Controle::where('niveau_critique', 'critique')->count(),
            'eleve' => Controle::where('niveau_critique', 'eleve')->count(),
            'moyen' => Controle::where('niveau_critique', 'moyen')->count(),
            'faible' => Controle::where('niveau_critique', 'faible')->count(),
        ];
        
        // Récupérer l'audit en cours
        $user = Auth::user();
        $audit = null;
        $evaluations = [];
        
        if ($user && $user->auditeur) {
            $audit = Audit::where('auditeur_id', $user->auditeur->id)
                ->where('status', 'en_cours')
                ->latest()
                ->first();
            
            if ($audit) {
                $evaluations = Evaluation::where('audit_id', $audit->id)
                    ->get()
                    ->keyBy('controle_id');
            }
        }
        
        return view('pages.audits.controls', [
            'title' => "Contrôles ISO 27002" . ($section ? " - $section" : ""),
            'controls' => $controls,
            'categories' => $categories,
            'currentSection' => $section,
            'stats' => $stats,
            'audit' => $audit,
            'evaluations' => $evaluations
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

    
    /**
     * Afficher toutes les clauses de l'audit
     */
    public function clauses($id)
    {
        $audit = Audit::with(['clauseEvaluations.clause'])->findOrFail($id);
        
        return view('pages.audits.clauses', [
            'title' => 'Clauses ISO 27001 - ' . $audit->titre,
            'audit' => $audit,
            'clauseEvaluations' => $audit->clauseEvaluations
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'une clause
     */
    public function editClause($auditId, $clauseEvalId)
    {
        $audit = Audit::findOrFail($auditId);
        $clauseEval = AuditClauseEvaluation::with('clause')->findOrFail($clauseEvalId);
        
        // Vérifier que la clause appartient bien à l'audit
        if ($clauseEval->audit_id !== $audit->id) {
            abort(404);
        }
        
        return view('pages.audits.edit-clause', [
            'title' => 'Modifier la clause - ' . $clauseEval->clause->titre,
            'audit' => $audit,
            'clauseEval' => $clauseEval
        ]);
    }


    /**
     * Afficher les détails d'un contrôle
     */
    public function controlsShow($id)
    {
        $control = Controle::with(['clause', 'preuves'])->findOrFail($id);
        
        // Récupérer l'audit en cours pour l'évaluation
        $user = Auth::user();
        $currentAudit = null;
        $currentEvaluation = null;
        
        if ($user && $user->auditeur) {
            $currentAudit = Audit::where('auditeur_id', $user->auditeur->id)
                ->where('status', 'en_cours')
                ->latest()
                ->first();
            
            if ($currentAudit) {
                $currentEvaluation = Evaluation::where('audit_id', $currentAudit->id)
                    ->where('controle_id', $control->id)
                    ->first();
            }
        }
        
        // Récupérer tous les audits qui ont évalué ce contrôle
        $audits = Audit::whereHas('evaluations', function($query) use ($control) {
            $query->where('controle_id', $control->id);
        })->with(['evaluations' => function($query) use ($control) {
            $query->where('controle_id', $control->id);
        }, 'entreprise'])->get();
        
        return view('pages.audits.controls-show', [
            'title' => $control->code . ' - ' . $control->titre,
            'control' => $control,
            'currentAudit' => $currentAudit,
            'currentEvaluation' => $currentEvaluation,
            'audits' => $audits
        ]);
    }

    /**
     * Afficher le formulaire de traitement d'une clause non conforme
     */
    public function editClauseRisk($auditId, $clauseEvalId)
    {
        $audit = Audit::findOrFail($auditId);
        $clauseEval = AuditClauseEvaluation::with(['clause', 'clause.reference'])->findOrFail($clauseEvalId);
        
        // Vérifier que la clause appartient bien à l'audit
        if ($clauseEval->audit_id !== $audit->id) {
            abort(404);
        }
        
        return view('pages.audits.edit-clause-risk', [
            'title' => 'Traitement du risque - ' . $clauseEval->clause->titre,
            'audit' => $audit,
            'clauseEval' => $clauseEval
        ]);
    }

    /**
     * Mettre à jour le traitement du risque pour une clause
     */
    public function updateClauseRisk(Request $request, $auditId, $clauseEvalId)
    {
        $clauseEval = AuditClauseEvaluation::findOrFail($clauseEvalId);
        
        // Vérifier que la clause appartient bien à l'audit
        if ($clauseEval->audit_id !== $auditId) {
            abort(404);
        }
        
        $validated = $request->validate([
            'plan_action' => 'nullable|string',
            'responsable' => 'nullable|string|max:255',
            'date_echeance' => 'nullable|date',
            'priorite' => 'nullable|in:haute,moyenne,basse',
            'commentaires' => 'nullable|string',
            'statut_traitement' => 'nullable|in:a_traiter,en_cours,traite,valide',
        ]);
        
        // Mettre à jour les champs de traitement
        $clauseEval->update([
            'plan_action' => $validated['plan_action'] ?? null,
            'responsable' => $validated['responsable'] ?? null,
            'date_echeance' => $validated['date_echeance'] ?? null,
            'priorite' => $validated['priorite'] ?? null,
            'commentaires' => $validated['commentaires'] ?? null,
            'statut_traitement' => $validated['statut_traitement'] ?? 'a_traiter',
        ]);
        
        // Note: updateAuditIndicators n'affecte pas les champs de traitement,
        // mais on l'appelle quand même pour être cohérent
        $this->updateAuditIndicators($auditId);
        
        return redirect()->route('risk.management')
            ->with('success', 'Plan d\'action enregistré avec succès !');
    }

    /**
     * Afficher le tableau de bord des indicateurs de contrôle
     */
    public function indicators()
    {
        // Récupérer l'audit en cours
        $auditId = session('current_audit_id');
        
        if (!$auditId) {
            return redirect()->route('audit.current')->with('error', 'Veuillez sélectionner un audit d\'abord.');
        }
        
        $audit = Audit::with(['entreprise', 'evaluations.controle', 'clauseEvaluations.clause'])
            ->findOrFail($auditId);
        
        // Utiliser les indicateurs sauvegardés ou recalculer si nécessaire
        $controlsByStatus = [
            'non_audite' => $audit->evaluations->where('statut', 'non_audite')->count(),
            'conforme' => $audit->evaluations->where('statut', 'conforme')->count(),
            'non_conforme' => $audit->evaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->evaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->evaluations->where('statut', 'non_applicable')->count(),
        ];
        
        $controlsByRisk = [
            'critique' => $audit->evaluations->where('niveau_risque_calcule', 'critique')->count(),
            'eleve' => $audit->evaluations->where('niveau_risque_calcule', 'eleve')->count(),
            'moyen' => $audit->evaluations->where('niveau_risque_calcule', 'moyen')->count(),
            'faible' => $audit->evaluations->where('niveau_risque_calcule', 'faible')->count(),
            'non_evalue' => $audit->evaluations->whereNull('niveau_risque_calcule')->count(),
        ];
        
        $controlsByCategory = $audit->evaluations->groupBy(function($evaluation) {
            return $evaluation->controle->categorie ?? 'Autres';
        })->map(function($group) {
            return [
                'total' => $group->count(),
                'conformes' => $group->where('statut', 'conforme')->count(),
                'non_conformes' => $group->where('statut', 'non_conforme')->count(),
                'taux_conformite' => $group->count() > 0 
                    ? round(($group->where('statut', 'conforme')->count() / $group->count()) * 100, 2)
                    : 0
            ];
        });
        
        $clausesByStatus = [
            'non_audite' => $audit->clauseEvaluations->where('statut', 'non_audite')->count(),
            'conforme' => $audit->clauseEvaluations->where('statut', 'conforme')->count(),
            'non_conforme' => $audit->clauseEvaluations->where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => $audit->clauseEvaluations->where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => $audit->clauseEvaluations->where('statut', 'non_applicable')->count(),
        ];
        
        $scoreByCategory = $audit->evaluations->groupBy(function($evaluation) {
            return $evaluation->controle->categorie ?? 'Autres';
        })->map(function($group) {
            $totalPoints = 0;
            $maxPoints = 0;
            foreach ($group as $evaluation) {
                $ponderation = $evaluation->controle->ponderation_defaut ?? 1;
                $maxPoints += $ponderation;
                if ($evaluation->statut === 'conforme') {
                    $totalPoints += $ponderation;
                } elseif ($evaluation->statut === 'partiellement_conforme') {
                    $totalPoints += $ponderation * 0.5;
                }
            }
            return $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
        });
        
        $daysSinceStart = now()->diffInDays($audit->date_debut);
        $daysRemaining = $audit->date_fin ? now()->diffInDays($audit->date_fin, false) : null;
        
        return view('pages.audits.indicators', [
            'title' => 'Indicateurs de contrôle - ' . $audit->titre,
            'audit' => $audit,
            'totalControls' => $audit->evaluations->count(),
            'controlsByStatus' => $controlsByStatus,
            'conformityRate' => $audit->conformity_rate ?? 0,
            'progressRate' => $audit->progress_rate ?? 0,
            'controlsByRisk' => $controlsByRisk,
            'controlsByCategory' => $controlsByCategory,
            'totalClauses' => $audit->clauseEvaluations->count(),
            'clausesByStatus' => $clausesByStatus,
            'clauseConformityRate' => $audit->clause_conformity_rate ?? 0,
            'daysSinceStart' => $daysSinceStart,
            'daysRemaining' => $daysRemaining,
            'scoreTotal' => $audit->score_total ?? 0,
            'scoreByCategory' => $scoreByCategory
        ]);
    }

     /**
     * Calculer et mettre à jour tous les indicateurs d'un audit
     * Basé sur les évaluations des contrôles ET des clauses
     */
    private function updateAuditIndicators($auditId)
    {
        $audit = Audit::with(['evaluations.controle', 'clauseEvaluations'])->find($auditId);
        if (!$audit) return;
        
        // ============================================
        // 1. INDICATEURS DES CONTRÔLES (ISO 27002)
        // ============================================
        $evaluations = $audit->evaluations;
        $totalControls = $evaluations->count();
        $conformControls = $evaluations->where('statut', 'conforme')->count();
        $nonConformControls = $evaluations->where('statut', 'non_conforme')->count();
        $partialConformControls = $evaluations->where('statut', 'partiellement_conforme')->count();
        $evaluatedControls = $evaluations->where('statut', '!=', 'non_audite')->count();
        
        // Taux de progression des contrôles
        $progressRateControls = $totalControls > 0 ? round(($evaluatedControls / $totalControls) * 100, 2) : 0;
        
        // Taux de conformité des contrôles
        $conformityRateControls = $totalControls > 0 ? round(($conformControls / $totalControls) * 100, 2) : 0;
        
        // Score total des contrôles avec pondération
        $totalPointsControls = 0;
        $maxPointsControls = 0;
        foreach ($evaluations as $evaluation) {
            $ponderation = $evaluation->controle->ponderation_defaut ?? 1;
            $maxPointsControls += $ponderation;
            if ($evaluation->statut === 'conforme') {
                $totalPointsControls += $ponderation;
            } elseif ($evaluation->statut === 'partiellement_conforme') {
                $totalPointsControls += $ponderation * 0.5;
            }
        }
        $scoreControls = $maxPointsControls > 0 ? round(($totalPointsControls / $maxPointsControls) * 100, 2) : 0;
        
        // ============================================
        // 2. INDICATEURS DES CLAUSES (ISO 27001)
        // ============================================
        $clauseEvaluations = $audit->clauseEvaluations;
        $totalClauses = $clauseEvaluations->count();
        $conformClauses = $clauseEvaluations->where('statut', 'conforme')->count();
        $nonConformClauses = $clauseEvaluations->where('statut', 'non_conforme')->count();
        $partialConformClauses = $clauseEvaluations->where('statut', 'partiellement_conforme')->count();
        $evaluatedClauses = $clauseEvaluations->where('statut', '!=', 'non_audite')->count();
        
        // Taux de progression des clauses
        $progressRateClauses = $totalClauses > 0 ? round(($evaluatedClauses / $totalClauses) * 100, 2) : 0;
        
        // Taux de conformité des clauses
        $conformityRateClauses = $totalClauses > 0 ? round(($conformClauses / $totalClauses) * 100, 2) : 0;
        
        // Score des clauses
        $totalPointsClauses = 0;
        $maxPointsClauses = 0;
        foreach ($clauseEvaluations as $clauseEval) {
            $maxPointsClauses += 100; // Score max par clause = 100
            $totalPointsClauses += $clauseEval->score ?? 0;
        }
        $scoreClauses = $maxPointsClauses > 0 ? round(($totalPointsClauses / $maxPointsClauses) * 100, 2) : 0;
        
        // ============================================
        // 3. INDICATEURS GLOBAUX (Moyennes pondérées)
        // ============================================
        // Progression globale (moyenne entre contrôles et clauses)
        $globalProgress = ($progressRateControls + $progressRateClauses) / 2;
        
        // Conformité globale (moyenne entre contrôles et clauses)
        $globalConformity = ($conformityRateControls + $conformityRateClauses) / 2;
        
        // Score global (moyenne entre score contrôles et score clauses)
        $globalScore = ($scoreControls + $scoreClauses) / 2;
        
        // ============================================
        // 4. INDICATEURS DE RISQUES (des contrôles)
        // ============================================
        $critiqueRisks = $evaluations->where('niveau_risque_calcule', 'critique')->count();
        $highRisks = $evaluations->where('niveau_risque_calcule', 'eleve')->count();
        $mediumRisks = $evaluations->where('niveau_risque_calcule', 'moyen')->count();
        $lowRisks = $evaluations->where('niveau_risque_calcule', 'faible')->count();
        
        // ============================================
        // 5. SAUVEGARDE DANS LA BASE
        // ============================================
        $audit->update([
            // Scores
            'score_total' => $globalScore,
            
            // Progressions
            'progress_rate' => $globalProgress,
            
            // Conformités
            'conformity_rate' => $globalConformity,
            'clause_conformity_rate' => $conformityRateClauses,
            
            // Compteurs contrôles
            'non_conform_count' => $nonConformControls,
            
            // Risques
            'critique_risk_count' => $critiqueRisks,
            'high_risk_count' => $highRisks,
        ]);
        
        // Log pour déboguer
        \Log::info('Indicateurs mis à jour', [
            'audit_id' => $auditId,
            'global_progress' => $globalProgress,
            'global_conformity' => $globalConformity,
            'controls_progress' => $progressRateControls,
            'clauses_progress' => $progressRateClauses,
            'total_controls' => $totalControls,
            'total_clauses' => $totalClauses,
            'evaluated_controls' => $evaluatedControls,
            'evaluated_clauses' => $evaluatedClauses,
        ]);
        
        return [
            'global_progress' => $globalProgress,
            'global_conformity' => $globalConformity,
            'global_score' => $globalScore,
            'controls' => [
                'total' => $totalControls,
                'evaluated' => $evaluatedControls,
                'conform' => $conformControls,
                'non_conform' => $nonConformControls,
                'progress_rate' => $progressRateControls,
                'conformity_rate' => $conformityRateControls,
                'score' => $scoreControls,
            ],
            'clauses' => [
                'total' => $totalClauses,
                'evaluated' => $evaluatedClauses,
                'conform' => $conformClauses,
                'non_conform' => $nonConformClauses,
                'progress_rate' => $progressRateClauses,
                'conformity_rate' => $conformityRateClauses,
                'score' => $scoreClauses,
            ],
            'risks' => [
                'critique' => $critiqueRisks,
                'high' => $highRisks,
                'medium' => $mediumRisks,
                'low' => $lowRisks,
            ]
        ];
    }

        /**
     * Mettre à jour l'évaluation d'une clause
     */
    public function updateClause(Request $request, $auditId, $clauseEvalId)
    {
        $clauseEval = AuditClauseEvaluation::findOrFail($clauseEvalId);
        
        // Vérifier que la clause appartient bien à l'audit
        if ($clauseEval->audit_id !== $auditId) {
            abort(404);
        }
        
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
        
        // Mettre à jour tous les indicateurs de l'audit (pour les clauses aussi)
        $this->updateAuditIndicators($auditId);
        
        return redirect()->route('audits.show', $auditId)
            ->with('success', 'Clause mise à jour avec succès !');
    }

    /**
     * Recalculer tous les indicateurs d'un audit (utile pour corriger des données)
     */
    public function recalculateIndicators($id)
    {
        $audit = Audit::findOrFail($id);
        
        // Recalculer tous les indicateurs
        $indicators = $this->updateAuditIndicators($id);
        
        return redirect()->route('audits.show', $id)
            ->with('success', 'Indicateurs recalculés : Score ' . $indicators['score_total'] . 
                '%, Conformité ' . $indicators['conformity_rate'] . 
                '%, Progression ' . $indicators['progress_rate'] . '%');
    }

/**
 * Mettre à jour l'évaluation d'un contrôle
 */
public function updateControl(Request $request, $auditId, $evaluationId)
{
    $evaluation = Evaluation::findOrFail($evaluationId);
    
    // Vérifier que le contrôle appartient bien à l'audit
    if ($evaluation->audit_id !== $auditId) {
        abort(404);
    }
    
    $validated = $request->validate([
        'statut' => 'required|in:non_audite,conforme,non_conforme,partiellement_conforme,non_applicable',
        'justification' => 'nullable|string',
        'recommandation' => 'nullable|string',
        'gravite_risque' => 'nullable|in:faible,moyen,eleve,critique',
        'probabilite_risque' => 'nullable|in:faible,moyen,eleve',
        'points_obtenus' => 'nullable|numeric|min:0',
        'preuves_consultees' => 'nullable|array',
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
        'preuves_consultees' => json_encode($validated['preuves_consultees'] ?? []),
        'date_evaluation' => now(),
    ]);
    
    // Mettre à jour tous les indicateurs de l'audit
    $this->updateAuditIndicators($auditId);
    
    return redirect()->route('audits.show', $auditId)
        ->with('success', 'Contrôle mis à jour avec succès !');
}


}