<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Entreprise;
use App\Models\Controle;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques globales
        $stats = [
            'total_audits' => Audit::count(),
            'in_progress_audits' => Audit::where('status', 'en_cours')->count(),
            'total_controls' => Controle::count(),
            'recent_audits' => Audit::with('entreprise')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Récupérer l'audit en cours
        $currentAudit = null;
        if ($user->auditeur) {
            $currentAudit = Audit::where('auditeur_id', $user->auditeur->id)
                ->where('status', 'en_cours')
                ->latest()
                ->first();
        }
        
        if ($currentAudit) {
            // Statistiques des contrôles
            $evaluations = $currentAudit->evaluations;
            $totalEvals = $evaluations->count();
            $conformControls = $evaluations->where('statut', 'conforme')->count();
            $nonConformControls = $evaluations->where('statut', 'non_conforme')->count();
            $partialControls = $evaluations->where('statut', 'partiellement_conforme')->count();
            $nonAuditeControls = $evaluations->where('statut', 'non_audite')->count();
            
            // Calcul des taux
            $controlsProgress = $totalEvals > 0 
                ? round((($totalEvals - $nonAuditeControls) / $totalEvals) * 100, 2)
                : 0;
            
            $conformityRate = $totalEvals > 0 
                ? round(($conformControls / $totalEvals) * 100, 2)
                : 0;
            
            // Statistiques des risques
            $risks = [
                'critique' => $evaluations->where('niveau_risque_calcule', 'critique')->count(),
                'eleve' => $evaluations->where('niveau_risque_calcule', 'eleve')->count(),
                'moyen' => $evaluations->where('niveau_risque_calcule', 'moyen')->count(),
                'faible' => $evaluations->where('niveau_risque_calcule', 'faible')->count(),
                'total' => $evaluations->whereNotNull('niveau_risque_calcule')->count(),
            ];
            
            $stats['controls_progress'] = $controlsProgress;
            $stats['conformity_rate'] = $conformityRate;
            $stats['total_evaluations'] = $totalEvals;
            $stats['conform_controls'] = $conformControls;
            $stats['non_conform_controls'] = $nonConformControls;
            $stats['controls_status'] = [
                'conforme' => $conformControls,
                'non_conforme' => $nonConformControls,
                'partiellement_conforme' => $partialControls,
                'non_audite' => $nonAuditeControls,
            ];
            $stats['risks'] = $risks;
        } else {
            $stats['controls_progress'] = 0;
            $stats['conformity_rate'] = 0;
            $stats['total_evaluations'] = 0;
            $stats['conform_controls'] = 0;
            $stats['non_conform_controls'] = 0;
            $stats['controls_status'] = [
                'conforme' => 0,
                'non_conforme' => 0,
                'partiellement_conforme' => 0,
                'non_audite' => 0,
            ];
            $stats['risks'] = ['critique' => 0, 'eleve' => 0, 'moyen' => 0, 'faible' => 0, 'total' => 0];
        }
        
        return view('pages.dashboard.index', [
            'stats' => $stats,
            'currentAudit' => $currentAudit
        ]);
    }

    private function getConformityRate()
    {
        $total = Evaluation::count();
        if ($total === 0) return 0;
        
        $conform = Evaluation::where('statut', 'conforme')->count();
        return round(($conform / $total) * 100, 1);
    }

    private function getControlsStatus()
    {
        return [
            'conforme' => Evaluation::where('statut', 'conforme')->count(),
            'non_conforme' => Evaluation::where('statut', 'non_conforme')->count(),
            'partiellement_conforme' => Evaluation::where('statut', 'partiellement_conforme')->count(),
            'non_applicable' => Evaluation::where('statut', 'non_applicable')->count(),
            'non_audite' => Evaluation::where('statut', 'non_audite')->count(),
        ];
    }

    private function getTopNonConformities()
    {
        return Evaluation::with('controle')
            ->where('statut', 'non_conforme')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($evaluation) {
                return [
                    'controle' => $evaluation->controle->code ?? 'N/A',
                    'titre' => $evaluation->controle->titre ?? 'Non défini',
                    'justification' => $evaluation->justification,
                    'gravite' => $evaluation->gravite_risque
                ];
            });
    }
}