<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Evaluation;
use App\Models\AuditClauseEvaluation;
use App\Models\Rapport;
use App\Models\Controle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function audit($auditId = null)
    {
        $query = Audit::with([
            'entreprise', 
            'auditeur.utilisateur', 
            'evaluations.controle', 
            'clauseEvaluations.clause',
            'rapport',
            'references'
        ]);

        $audit = $auditId 
            ? $query->findOrFail($auditId) 
            : $query->latest()->first();
        
        if (!$audit) {
            return redirect()->route('audits.create')
                ->with('info', 'Veuillez d\'abord créer un audit.');
        }

        if (!$auditId) {
            return redirect()->route('report.index', ['id' => $audit->id]);
        }
        
        // Préparer toutes les données dynamiques pour le rapport
        $reportData = $this->prepareReportData($audit);
        
        return view('reports.audit', [
            'title' => 'Rapport d\'audit - ' . ($audit->entreprise->nom ?? 'Sans nom'),
            'audit' => $audit,
            'rapport' => $audit->rapport ?? new Rapport(),
            'data' => $reportData
        ]);
    }
    
    private function prepareReportData($audit)
    {
        $evaluations = $audit->evaluations;
        $clauseEvaluations = $audit->clauseEvaluations;
        
        $totalControls = $evaluations->count();
        $conforme = $evaluations->where('statut', 'conforme')->count();
        $nonConforme = $evaluations->where('statut', 'non_conforme')->count();
        $partiel = $evaluations->where('statut', 'partiellement_conforme')->count();
        $nonApplicable = $evaluations->where('statut', 'non_applicable')->count();
        $nonAudite = $evaluations->where('statut', 'non_audite')->count();
        
        $effectiveTotal = ($totalControls - $nonApplicable) ?: 1;
        $conformityRate = round(($conforme / $effectiveTotal) * 100, 1);
        $progressRate = $totalControls > 0 ? round((($totalControls - $nonAudite) / $totalControls) * 100, 1) : 0;
        
        // Scores par domaine
        $scores = [
            'organisationnel' => $this->calculateDomainScore($evaluations, ['organisation', 'gouvernance', 'politique', 'politiques']),
            'personnes' => $this->calculateDomainScore($evaluations, ['rh', 'personne', 'sensibilisation', 'humain', 'competence']),
            'physique' => $this->calculateDomainScore($evaluations, ['physique', 'acces', 'locaux', 'bâtiment', 'securite physique']),
            'technologique' => $this->calculateDomainScore($evaluations, ['technologie', 'reseau', 'systeme', 'informatique', 'logique']),
        ];
        
        // Liste des non-conformités avec leurs traitements
        $nonConformites = $evaluations->filter(function($e) {
            return $e->statut === 'non_conforme';
        })->map(function($e) {
            return [
                'id' => $e->id,
                'code' => $e->controle->code,
                'titre' => $e->controle->titre,
                'description' => $e->controle->description,
                'justification' => $e->justification,
                'recommandation' => $e->recommandation,
                'niveau_risque' => $e->niveau_risque_calcule,
                'gravite_risque' => $e->gravite_risque,
                'probabilite_risque' => $e->probabilite_risque,
                'points_obtenus' => $e->points_obtenus,
                // Champs de traitement (depuis la table evaluations ou une relation)
                'plan_action' => $e->plan_action ?? null,
                'responsable' => $e->responsable ?? null,
                'date_echeance' => $e->date_echeance ?? null,
                'priorite' => $e->priorite ?? null,
                'commentaires' => $e->commentaires ?? null,
                'statut_traitement' => $e->statut_traitement ?? 'a_traiter',
            ];
        });
        
        // Statistiques des traitements
        $traitementStats = [
            'a_traiter' => $nonConformites->where('statut_traitement', 'a_traiter')->count(),
            'en_cours' => $nonConformites->where('statut_traitement', 'en_cours')->count(),
            'traite' => $nonConformites->where('statut_traitement', 'traite')->count(),
            'valide' => $nonConformites->where('statut_traitement', 'valide')->count(),
            'total' => $nonConformites->count(),
            'taux_traitement' => $nonConformites->count() > 0 
                ? round((($nonConformites->where('statut_traitement', 'traite')->count() + 
                        $nonConformites->where('statut_traitement', 'valide')->count()) / $nonConformites->count()) * 100, 1)
                : 100,
        ];
        
        // Répartition des priorités
        $prioriteStats = [
            'haute' => $nonConformites->where('priorite', 'haute')->count(),
            'moyenne' => $nonConformites->where('priorite', 'moyenne')->count(),
            'basse' => $nonConformites->where('priorite', 'basse')->count(),
            'non_definie' => $nonConformites->whereNull('priorite')->count(),
        ];
        
        // Contrôles par catégorie
        $controlsByCategory = $evaluations->groupBy(function($e) {
            return $e->controle->categorie ?? 'Autres';
        })->map(function($group) {
            return [
                'controls' => $group,
                'total' => $group->count(),
                'conforme' => $group->where('statut', 'conforme')->count(),
                'non_conforme' => $group->where('statut', 'non_conforme')->count(),
                'partiel' => $group->where('statut', 'partiellement_conforme')->count(),
                'non_applicable' => $group->where('statut', 'non_applicable')->count(),
                'non_audite' => $group->where('statut', 'non_audite')->count(),
                'taux_conformite' => $group->count() > 0 
                    ? round(($group->where('statut', 'conforme')->count() / ($group->count() - $group->where('statut', 'non_applicable')->count())) * 100, 1)
                    : 0,
            ];
        });
        
        // Risques par niveau
        $risksByLevel = [
            'critique' => $evaluations->where('niveau_risque_calcule', 'critique')->count(),
            'eleve' => $evaluations->where('niveau_risque_calcule', 'eleve')->count(),
            'moyen' => $evaluations->where('niveau_risque_calcule', 'moyen')->count(),
            'faible' => $evaluations->where('niveau_risque_calcule', 'faible')->count(),
            'non_evalue' => $evaluations->whereNull('niveau_risque_calcule')->count(),
        ];
        
        // Statistiques des clauses ISO 27001
        $totalClauses = $clauseEvaluations->count();
        $clausesConformes = $clauseEvaluations->where('statut', 'conforme')->count();
        $clausesNonConformes = $clauseEvaluations->where('statut', 'non_conforme')->count();
        $clausesPartiel = $clauseEvaluations->where('statut', 'partiellement_conforme')->count();
        $clausesNonAudite = $clauseEvaluations->where('statut', 'non_audite')->count();
        
        $clauseConformityRate = $totalClauses > 0 
            ? round(($clausesConformes / ($totalClauses - $clausesNonAudite)) * 100, 1)
            : 0;
        
        // Non-conformités des clauses avec traitements
        $clausesNonConformesList = $clauseEvaluations->filter(function($c) {
            return $c->statut === 'non_conforme';
        })->map(function($c) {
            return [
                'id' => $c->id,
                'numero' => $c->clause->numero,
                'titre' => $c->clause->titre,
                'description' => $c->clause->description,
                'justification' => $c->justification,
                'recommandation' => $c->recommandation,
                'niveau_conformite' => $c->niveau_conformite,
                'score' => $c->score,
                'plan_action' => $c->plan_action ?? null,
                'responsable' => $c->responsable ?? null,
                'date_echeance' => $c->date_echeance ?? null,
                'priorite' => $c->priorite ?? null,
                'commentaires' => $c->commentaires ?? null,
                'statut_traitement' => $c->statut_traitement ?? 'a_traiter',
            ];
        });
        
        // Statistiques des traitements des clauses
        $clauseTraitementStats = [
            'a_traiter' => $clausesNonConformesList->where('statut_traitement', 'a_traiter')->count(),
            'en_cours' => $clausesNonConformesList->where('statut_traitement', 'en_cours')->count(),
            'traite' => $clausesNonConformesList->where('statut_traitement', 'traite')->count(),
            'valide' => $clausesNonConformesList->where('statut_traitement', 'valide')->count(),
            'total' => $clausesNonConformesList->count(),
        ];
        
        // Calcul du score global
        $totalPoints = 0;
        $maxPoints = 0;
        foreach ($evaluations as $evaluation) {
            $ponderation = $evaluation->controle->ponderation_defaut ?? 1;
            $maxPoints += $ponderation;
            if ($evaluation->statut === 'conforme') {
                $totalPoints += $ponderation;
            } elseif ($evaluation->statut === 'partiellement_conforme') {
                $totalPoints += $ponderation * 0.5;
            }
        }
        $globalScore = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
        
        // Score par catégorie
        $scoreByCategory = $evaluations->groupBy(function($e) {
            return $e->controle->categorie ?? 'Autres';
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
            return $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
        });
        
        return [
            'stats' => [
                'total_controls' => $totalControls,
                'conforme' => $conforme,
                'non_conforme' => $nonConforme,
                'partiel' => $partiel,
                'non_applicable' => $nonApplicable,
                'non_audite' => $nonAudite,
                'conformity_rate' => $conformityRate,
                'progress_rate' => $progressRate,
                'scores' => $scores,
                'global_score' => $globalScore,
                'score_by_category' => $scoreByCategory,
            ],
            'non_conformites' => $nonConformites,
            'traitement_stats' => $traitementStats,
            'priorite_stats' => $prioriteStats,
            'controls_by_category' => $controlsByCategory,
            'risks_by_level' => $risksByLevel,
            'clauses' => [
                'total' => $totalClauses,
                'conformes' => $clausesConformes,
                'non_conformes' => $clausesNonConformes,
                'partiel' => $clausesPartiel,
                'non_audite' => $clausesNonAudite,
                'conformity_rate' => $clauseConformityRate,
                'non_conformites' => $clausesNonConformesList,
                'traitement_stats' => $clauseTraitementStats,
            ],
            'generated_at' => now()->format('d/m/Y H:i'),
        ];
    }
    
    private function calculateDomainScore($evaluations, $keywords)
    {
        $filtered = $evaluations->filter(function($eval) use ($keywords) {
            $categorie = strtolower($eval->controle->categorie ?? '');
            foreach ($keywords as $keyword) {
                if (str_contains($categorie, $keyword)) return true;
            }
            return false;
        });
        
        $total = $filtered->count();
        if ($total === 0) return 0;
        
        $conforme = $filtered->where('statut', 'conforme')->count();
        return round(($conforme / $total) * 5, 1);
    }
    
    public function storeReport(Request $request, $id)
    {
        $audit = Audit::findOrFail($id);
        
        Rapport::updateOrCreate(
            ['audit_id' => $id],
            $request->except('_token')
        );
        
        return redirect()->route('report.index', $id)
            ->with('success', 'Le rapport a été enregistré avec succès.');
    }
    
    public function history()
    {
        $audits = Audit::with(['entreprise', 'rapport'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('reports.history', [
            'title' => 'Historique des Audits',
            'audits' => $audits
        ]);
    }
    
    public function statistics()
    {
        $stats = [
            'total_audits' => Audit::count(),
            'total_controls' => Controle::count(),
            'global_conformity_rate' => $this->getGlobalConformityRate(),
            'audits_by_status' => Audit::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status'),
            'monthly_audits' => Audit::select(
                    DB::raw('strftime("%Y-%m", created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
            'top_non_conformities' => $this->getTopNonConformities(),
        ];
        
        return view('reports.statistics', [
            'title' => 'Tableau de Bord des Statistiques',
            'stats' => $stats
        ]);
    }
    
    private function getGlobalConformityRate()
    {
        $total = Evaluation::count();
        if ($total === 0) return 0;
        $conforme = Evaluation::where('statut', 'conforme')->count();
        return round(($conforme / $total) * 100, 1);
    }
    
    private function getTopNonConformities()
    {
        return Evaluation::with('controle')
            ->where('statut', 'non_conforme')
            ->select('controle_id', DB::raw('count(*) as total'))
            ->groupBy('controle_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }
}