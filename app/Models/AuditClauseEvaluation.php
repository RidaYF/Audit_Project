<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditClauseEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'audit_id',
        'clause_id',
        'statut',
        'justification',
        'recommandation',
        'niveau_conformite',
        'score',
        'plan_action',      
        'responsable',       
        'date_echeance',     
        'priorite',          
        'commentaires',      
        'statut_traitement'  
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'score' => 'float'
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function clause()
    {
        return $this->belongsTo(Clause::class, 'clause_id');
    }
}