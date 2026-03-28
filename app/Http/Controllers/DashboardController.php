<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Entreprise;
use App\Models\Controle;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer les données pour le dashboard
        $stats = [
            'total_audits' => Audit::count(),
            'completed_audits' => Audit::where('status', 'termine')->count(),
            'in_progress_audits' => Audit::where('status', 'en_cours')->count(),
            'total_controls' => Controle::count(),
            'conformity_rate' => $this->getConformityRate(),
            'recent_audits' => Audit::with(['entreprise', 'auditeur'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'controls_status' => $this->getControlsStatus(),
            'top_non_conformities' => $this->getTopNonConformities(),
        ];

        // Récupérer l'audit en cours (pour démonstration)
        $currentAudit = Audit::with(['entreprise', 'auditeur', 'references'])
            ->where('status', 'en_cours')
            ->first();

        return view('pages.dashboard.dashboard', [
            'title' => 'Audit Dashboard',
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