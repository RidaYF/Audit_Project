<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    /**
     * Afficher la gestion des risques (ISO 27005)
     * Avec filtrage des non-conformités uniquement
     */
    public function index()
    {
        // Récupérer l'audit en cours (depuis la session ou un paramètre)
        $auditId = session('current_audit_id'); // ou request()->get('audit_id')
        $audit = Audit::with('entreprise')->findOrFail($auditId);
        
        // Récupérer UNIQUEMENT les évaluations non conformes
        $nonConformites = Evaluation::with(['controle', 'controle.clause', 'controle.clause.reference'])
            ->where('audit_id', $audit->id)
            ->where('statut', 'non_conforme')  // Filtrer uniquement les non-conformités
            ->orderByRaw("
                CASE niveau_risque_calcule
                    WHEN 'critique' THEN 1
                    WHEN 'eleve' THEN 2
                    WHEN 'moyen' THEN 3
                    WHEN 'faible' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques pour l'affichage
        $stats = [
            'critiques' => $nonConformites->where('niveau_risque_calcule', 'critique')->count(),
            'eleves' => $nonConformites->where('niveau_risque_calcule', 'eleve')->count(),
            'moyens' => $nonConformites->where('niveau_risque_calcule', 'moyen')->count(),
            'total' => $nonConformites->count()
        ];
        
        return view('pages.audit.risk', [
            'title' => 'Gestion des risques - Non-conformités ISO 27005',
            'audit' => $audit,
            'nonConformites' => $nonConformites,
            'stats' => $stats
        ]);
    }
    
    /**
     * Alternative : Afficher avec option de filtre (tous / non-conformes / conformes)
     */
    public function indexWithFilter(Request $request)
    {
        $auditId = session('current_audit_id');
        $audit = Audit::with('entreprise')->findOrFail($auditId);
        $filter = $request->get('filter', 'non_conformes'); // non_conformes, conformes, tous
        
        $query = Evaluation::with(['controle', 'controle.clause'])
            ->where('audit_id', $audit->id);
        
        switch ($filter) {
            case 'conformes':
                $query->where('statut', 'conforme');
                break;
            case 'non_conformes':
                $query->where('statut', 'non_conforme');
                break;
            // 'tous' : pas de filtre
        }
        
        $evaluations = $query->orderByRaw("
                CASE niveau_risque_calcule
                    WHEN 'critique' THEN 1
                    WHEN 'eleve' THEN 2
                    WHEN 'moyen' THEN 3
                    WHEN 'faible' THEN 4
                    ELSE 5
                END
            ")
            ->get();
        
        return view('pages.audit.risk', [
            'audit' => $audit,
            'evaluations' => $evaluations,
            'currentFilter' => $filter,
            'title' => 'Gestion des risques ISO 27005'
        ]);
    }
}