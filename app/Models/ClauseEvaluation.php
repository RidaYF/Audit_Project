<?php
// app/Models/ClauseEvaluation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClauseEvaluation extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'clause_evaluations';
    
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
        'commentaire_formateur',
        'date_evaluation'
    ];
    
    protected $casts = [
        'score' => 'float',
        'date_evaluation' => 'datetime'
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