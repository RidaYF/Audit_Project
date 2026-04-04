<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Evaluation;
use App\Models\Rapport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Affiche le rapport d'audit (Vue Rédaction/Consultation)
     */
    public function audit($auditId = null)
    {
        // 1. On charge l'audit avec toutes ses relations pour éviter les requêtes N+1
        $query = Audit::with(['entreprise', 'auditeur', 'evaluations.controle', 'rapport']);

        // 2. Récupération de l'audit (spécifique ou le dernier en date)
        $audit = $auditId 
            ? $query->find($auditId) 
            : $query->latest()->first();
        
        // 3. Sécurité : Si aucun audit n'existe dans la base de données
        if (!$audit) {
            return redirect()->route('audits.create')
                ->with('info', 'Veuillez d\'abord créer un audit pour pouvoir générer son rapport.');
        }

        // 4. Si on accède à /reports/audit sans ID, on redirige vers l'URL avec l'ID du dernier audit
        if (!$auditId) {
            return redirect()->route('report.index', ['id' => $audit->id]);
        }
        
        // 5. Calcul des statistiques de conformité pour le bloc "Synthèse"
        $summary = $this->generateSummary($audit);
        
        return view('reports.audit', [
            'title'   => 'Rapport d\'audit - ' . ($audit->entreprise->nom ?? 'Sans nom'),
            'audit'   => $audit,
            'summary' => $summary,
            'rapport' => $audit->rapport ?? new Rapport() // Retourne un objet vide si pas encore enregistré
        ]);
    }

    /**
     * Enregistre ou met à jour les sections textuelles et dynamiques du rapport
     */
    public function storeReport(Request $request, $id)
    {
        // Vérification de l'existence de l'audit
        $audit = Audit::findOrFail($id);

        // Sauvegarde via updateOrCreate pour gérer le premier enregistrement
        Rapport::updateOrCreate(
            ['audit_id' => $id],
            $request->only([
                'version', 
                'expert_nom', 
                'confidentialite_texte', 
                'cadre_mission',
                'methodologie', 
                'synthese', 
                'analyse_risques', 
                'introduction_plan_action',
                'equipe_expert', // Stocké en JSON
                'equipe_audite', // Stocké en JSON
                'planning_reel', // Stocké en JSON
                'outils_utilises' // Stocké en JSON
            ])
        );

        return redirect()->route('report.index', $id)
            ->with('success', 'Le contenu du rapport a été enregistré avec succès.');
    }
    
    /**
     * Affiche l'historique de tous les rapports générés
     */
   public function history()
{
    // On récupère TOUS les audits, et on "joint" le rapport s'il existe
    $audits = Audit::with(['entreprise', 'rapport'])
        ->latest()
        ->paginate(10);
    
    return view('reports.history', [
        'title'  => 'Historique des Audits',
        'audits' => $audits // On change le nom de la variable ici
    ]);
}
    
    /**
     * Affiche les statistiques globales de conformité
     */
    public function statistics()
    {
        $stats = [
            'total_audits'           => Audit::count(),
            'total_controls'         => \App\Models\Controle::count(),
            'global_conformity_rate' => $this->getGlobalConformityRate(),
            'audits_by_status'       => Audit::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status'),
            'monthly_audits'         => Audit::select(
                    DB::raw('strftime("%Y-%m", created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
            'top_non_conformities'   => $this->getTopNonConformities(),
        ];
        
        return view('reports.statistics', [
            'title' => 'Tableau de Bord des Statistiques',
            'stats' => $stats
        ]);
    }
    
    /**
     * Calcule le taux de conformité pour un audit spécifique
     */
    private function generateSummary($audit)
    {
        $evaluations = $audit->evaluations;
        $total = $evaluations->count();
        
        if ($total === 0) {
            return [
                'total' => 0, 'conforme' => 0, 'non_conforme' => 0,
                'partiellement' => 0, 'non_applicable' => 0, 'conformity_rate' => 0
            ];
        }
        
        $conforme      = $evaluations->where('statut', 'conforme')->count();
        $nonConforme   = $evaluations->where('statut', 'non_conforme')->count();
        $partiellement = $evaluations->where('statut', 'partiellement_conforme')->count();
        $nonApplicable = $evaluations->where('statut', 'non_applicable')->count();
        
        // Calcul du taux : (Conformes / (Total - Non Applicables)) * 100
        $effectiveTotal = ($total - $nonApplicable) ?: 1;
        $rate = round(($conforme / $effectiveTotal) * 100, 1);
        
        return [
            'total'          => $total,
            'conforme'       => $conforme,
            'non_conforme'   => $nonConforme,
            'partiellement'  => $partiellement,
            'non_applicable' => $nonApplicable,
            'conformity_rate'=> $rate
        ];
    }
    
    /**
     * Taux de conformité global (toutes évaluations confondues)
     */
    private function getGlobalConformityRate()
    {
        $total = Evaluation::count();
        if ($total === 0) return 0;
        
        $conforme = Evaluation::where('statut', 'conforme')->count();
        return round(($conforme / $total) * 100, 1);
    }
    
    /**
     * Récupère les 5 contrôles les plus souvent en non-conformité
     */
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