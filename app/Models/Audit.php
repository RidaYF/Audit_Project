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
        'commentaire_global', 'signature_auditeur'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'score_total' => 'float',
        'temps_total' => 'integer'
    ];

    public function auditeur()
    {
        return $this->belongsTo(Auditeur::class, 'auditeur_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function references()
    {
        return $this->belongsToMany(Reference::class, 'audit_references')
                    ->withPivot('objectif_specifique', 'ponderation')
                    ->withTimestamps();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'audit_id');
    }

    public function rapport()
    {
        return $this->hasOne(Rapport::class, 'audit_id');
    }

    public function clauseEvaluations()
    {
        return $this->hasMany(AuditClauseEvaluation::class, 'audit_id');
    }
}