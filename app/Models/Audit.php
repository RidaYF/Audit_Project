<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'auditeur_id', 'entreprise_id', 'titre', 'description',
        'date_debut', 'date_fin', 'status', 'score_total', 'temps_total',
        'commentaire_global', 'signature_auditeur',
        // Nouveaux champs
        'progress_rate', 'conformity_rate',
        'total_controls', 'evaluated_controls', 'conform_controls', 
        'non_conform_controls', 'partial_conform_controls',
        'critique_risk_count', 'high_risk_count', 'medium_risk_count', 'low_risk_count',
        'total_clauses', 'conform_clauses', 'non_conform_clauses', 'clause_conformity_rate'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'score_total' => 'float',
        'progress_rate' => 'float',
        'conformity_rate' => 'float',
        'clause_conformity_rate' => 'float'
    ];

    public function auditeur()
    {
        return $this->belongsTo(Auditeur::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function references()
    {
        return $this->belongsToMany(Reference::class, 'audit_references');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function clauseEvaluations()
    {
        return $this->hasMany(AuditClauseEvaluation::class);
    }

    /**
     * Relation avec le rapport d'audit
     */
    public function rapport()
    {
        return $this->hasOne(Rapport::class);
    }

    // Accesseur pour le taux de progression
    public function getProgressPercentageAttribute()
    {
        return $this->progress_rate ?? 0;
    }

    // Accesseur pour le taux de conformité
    public function getConformityPercentageAttribute()
    {
        return $this->conformity_rate ?? 0;
    }
}