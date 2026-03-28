<?php
// app/Models/AuditClauseEvaluation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditClauseEvaluation extends Model
{
    use HasFactory;
    
    protected $table = 'audit_clause_evaluations';
    
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
        'score'
    ];
    
    protected $casts = [
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