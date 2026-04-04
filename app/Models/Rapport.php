<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rapport extends Model
{
    use HasUuids;

    protected $fillable = [
        'audit_id', 'version', 'expert_nom', 'confidentialite_texte', 
        'cadre_mission', 'methodologie', 'synthese', 'analyse_risques', 
        'introduction_plan_action', 'equipe_expert', 'equipe_audite', 
        'planning_reel', 'outils_utilises', 'statistiques', 'format'
    ];

    protected $casts = [
        'equipe_expert' => 'array',
        'equipe_audite' => 'array',
        'planning_reel' => 'array',
        'outils_utilises' => 'array',
        'statistiques' => 'array',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }
}