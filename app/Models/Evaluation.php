<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'audit_id', 'controle_id', 'statut', 'justification',
        'recommandation', 'gravite_risque', 'probabilite_risque',
        'niveau_risque_calcule', 'preuves_consultees', 'commentaire_formateur',
        'date_evaluation', 'points_obtenus'
    ];

    protected $casts = [
        'preuves_consultees' => 'array',
        'date_evaluation' => 'datetime',
        'points_obtenus' => 'float'
    ];

    // ============================================
    // RELATIONS
    // ============================================
    
    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function controle()
    {
        return $this->belongsTo(Controle::class, 'controle_id');
    }
    

    // ============================================
    // SCOPES (Filtres)
    // ============================================
    
    /**
     * Scope pour les évaluations conformes
     */
    public function scopeConformes(Builder $query): Builder
    {
        return $query->where('statut', 'conforme');
    }
    
    /**
     * Scope pour les évaluations non conformes
     */
    public function scopeNonConformes(Builder $query): Builder
    {
        return $query->where('statut', 'non_conforme');
    }
    
    /**
     * Scope pour les évaluations partiellement conformes
     */
    public function scopePartiellementConformes(Builder $query): Builder
    {
        return $query->where('statut', 'partiellement_conforme');
    }
    
    /**
     * Scope pour les évaluations non applicables
     */
    public function scopeNonApplicables(Builder $query): Builder
    {
        return $query->where('statut', 'non_applicable');
    }
    
    /**
     * Scope pour les évaluations non auditées
     */
    public function scopeNonAuditees(Builder $query): Builder
    {
        return $query->where('statut', 'non_audite');
    }
    
    /**
     * Scope pour les risques critiques (critique ou élevé)
     */
    public function scopeRisquesCritiques(Builder $query): Builder
    {
        return $query->whereIn('niveau_risque_calcule', ['critique', 'eleve']);
    }
    
    /**
     * Scope pour les risques par niveau
     */
    public function scopeNiveauRisque(Builder $query, string $niveau): Builder
    {
        return $query->where('niveau_risque_calcule', $niveau);
    }
    
    /**
     * Scope pour les évaluations avec points obtenus inférieurs au maximum
     */
    public function scopeNonParfaites(Builder $query): Builder
    {
        return $query->whereRaw('points_obtenus < (SELECT ponderation_defaut FROM controles WHERE controles.id = evaluations.controle_id)');
    }

    // ============================================
    // ACCESSORS (Getters)
    // ============================================
    
    /**
     * Obtenir la couleur CSS pour le statut
     */
    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'conforme' => 'green',
            'non_conforme' => 'red',
            'partiellement_conforme' => 'orange',
            'non_applicable' => 'gray',
            'non_audite' => 'blue',
            default => 'gray'
        };
    }
    
    /**
     * Obtenir l'icône pour le statut
     */
    public function getStatutIconAttribute(): string
    {
        return match($this->statut) {
            'conforme' => '✅',
            'non_conforme' => '❌',
            'partiellement_conforme' => '⚠️',
            'non_applicable' => '➖',
            'non_audite' => '⏳',
            default => '❓'
        };
    }
    
    /**
     * Obtenir le libellé français du statut
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'conforme' => 'Conforme',
            'non_conforme' => 'Non conforme',
            'partiellement_conforme' => 'Partiellement conforme',
            'non_applicable' => 'Non applicable',
            'non_audite' => 'Non audité',
            default => ucfirst($this->statut)
        };
    }
    
    /**
     * Obtenir la couleur CSS pour le niveau de risque
     */
    public function getRisqueColorAttribute(): string
    {
        return match($this->niveau_risque_calcule) {
            'critique' => 'red',
            'eleve' => 'orange',
            'moyen' => 'yellow',
            'faible' => 'green',
            default => 'gray'
        };
    }
    
    /**
     * Obtenir le libellé français du niveau de risque
     */
    public function getRisqueLabelAttribute(): string
    {
        return match($this->niveau_risque_calcule) {
            'critique' => 'Critique',
            'eleve' => 'Élevé',
            'moyen' => 'Moyen',
            'faible' => 'Faible',
            default => 'Non défini'
        };
    }
    
    /**
     * Obtenir la classe CSS pour le badge du niveau de risque
     */
    public function getRisqueBadgeClassAttribute(): string
    {
        return match($this->niveau_risque_calcule) {
            'critique' => 'bg-red-100 text-red-700 border-red-200',
            'eleve' => 'bg-orange-100 text-orange-700 border-orange-200',
            'moyen' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'faible' => 'bg-green-100 text-green-700 border-green-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200'
        };
    }
    
    /**
     * Vérifier si c'est une non-conformité
     */
    public function isNonConforme(): bool
    {
        return $this->statut === 'non_conforme';
    }
    
    /**
     * Vérifier si c'est conforme
     */
    public function isConforme(): bool
    {
        return $this->statut === 'conforme';
    }
    
    /**
     * Vérifier si c'est partiellement conforme
     */
    public function isPartiellementConforme(): bool
    {
        return $this->statut === 'partiellement_conforme';
    }
    
    /**
     * Vérifier si le risque est critique ou élevé
     */
    public function isRisqueCritique(): bool
    {
        return in_array($this->niveau_risque_calcule, ['critique', 'eleve']);
    }
    
    /**
     * Calculer le niveau de risque (si non défini)
     */
    public function calculerNiveauRisque(): ?string
    {
        if (!$this->gravite_risque || !$this->probabilite_risque) {
            return null;
        }
        
        $matrice = [
            'faible' => ['faible' => 'faible', 'moyen' => 'faible', 'eleve' => 'moyen'],
            'moyen' => ['faible' => 'faible', 'moyen' => 'moyen', 'eleve' => 'eleve'],
            'eleve' => ['faible' => 'moyen', 'moyen' => 'eleve', 'eleve' => 'critique'],
        ];
        
        return $matrice[$this->gravite_risque][$this->probabilite_risque] ?? null;
    }
    
    /**
     * Mettre à jour automatiquement le niveau de risque
     */
    public function updateNiveauRisque(): bool
    {
        $niveau = $this->calculerNiveauRisque();
        if ($niveau && $this->niveau_risque_calcule !== $niveau) {
            $this->niveau_risque_calcule = $niveau;
            return $this->save();
        }
        return false;
    }

    // ============================================
    // STATISTIQUES
    // ============================================
    
    /**
     * Obtenir les statistiques des évaluations pour un audit
     */
    public static function getStatsForAudit(string $auditId): array
    {
        $stats = self::where('audit_id', $auditId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'conforme' THEN 1 ELSE 0 END) as conformes,
                SUM(CASE WHEN statut = 'non_conforme' THEN 1 ELSE 0 END) as non_conformes,
                SUM(CASE WHEN statut = 'partiellement_conforme' THEN 1 ELSE 0 END) as partiellement_conformes,
                SUM(CASE WHEN statut = 'non_applicable' THEN 1 ELSE 0 END) as non_applicables,
                SUM(CASE WHEN niveau_risque_calcule = 'critique' THEN 1 ELSE 0 END) as risques_critiques,
                SUM(CASE WHEN niveau_risque_calcule = 'eleve' THEN 1 ELSE 0 END) as risques_eleves,
                SUM(CASE WHEN niveau_risque_calcule = 'moyen' THEN 1 ELSE 0 END) as risques_moyens,
                SUM(CASE WHEN niveau_risque_calcule = 'faible' THEN 1 ELSE 0 END) as risques_faibles,
                AVG(points_obtenus) as score_moyen
            ")
            ->first();
        
        return [
            'total' => (int) ($stats->total ?? 0),
            'conformes' => (int) ($stats->conformes ?? 0),
            'non_conformes' => (int) ($stats->non_conformes ?? 0),
            'partiellement_conformes' => (int) ($stats->partiellement_conformes ?? 0),
            'non_applicables' => (int) ($stats->non_applicables ?? 0),
            'risques_critiques' => (int) ($stats->risques_critiques ?? 0),
            'risques_eleves' => (int) ($stats->risques_eleves ?? 0),
            'risques_moyens' => (int) ($stats->risques_moyens ?? 0),
            'risques_faibles' => (int) ($stats->risques_faibles ?? 0),
            'score_moyen' => round($stats->score_moyen ?? 0, 2),
            'taux_conformite' => $stats->total > 0 
                ? round((($stats->conformes ?? 0) / $stats->total) * 100, 2)
                : 0
        ];
    }
}