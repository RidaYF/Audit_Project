<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function audit($auditId = null)
    {
        $audit = $auditId 
            ? Audit::with(['entreprise', 'auditeur', 'evaluations.controle'])->findOrFail($auditId)
            : Audit::with(['entreprise', 'auditeur', 'evaluations.controle'])
                ->latest()
                ->first();
        
        if (!$audit) {
            return redirect()->route('dashboard')->with('info', 'Aucun rapport disponible');
        }
        
        $summary = $this->generateSummary($audit);
        
        return view('reports.audit', [
            'title' => 'Rapport d\'audit',
            'audit' => $audit,
            'summary' => $summary
        ]);
    }
    
    public function history()
    {
        $audits = Audit::with(['entreprise', 'auditeur'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('reports.history', [
            'title' => 'Historique des audits',
            'audits' => $audits
        ]);
    }
    
    public function statistics()
    {
        // Statistiques globales
        $stats = [
            'total_audits' => Audit::count(),
            'total_controls' => \App\Models\Controle::count(),
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
            'title' => 'Statistiques de conformité',
            'stats' => $stats
        ]);
    }
    
    private function generateSummary($audit)
    {
        $evaluations = $audit->evaluations;
        $total = $evaluations->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'conforme' => 0,
                'non_conforme' => 0,
                'partiellement' => 0,
                'non_applicable' => 0,
                'conformity_rate' => 0
            ];
        }
        
        $conforme = $evaluations->where('statut', 'conforme')->count();
        $nonConforme = $evaluations->where('statut', 'non_conforme')->count();
        $partiellement = $evaluations->where('statut', 'partiellement_conforme')->count();
        $nonApplicable = $evaluations->where('statut', 'non_applicable')->count();
        
        return [
            'total' => $total,
            'conforme' => $conforme,
            'non_conforme' => $nonConforme,
            'partiellement' => $partiellement,
            'non_applicable' => $nonApplicable,
            'conformity_rate' => round(($conforme / ($total - $nonApplicable)) * 100, 1)
        ];
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