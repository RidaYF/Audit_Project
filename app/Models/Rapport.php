<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rapport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rapports';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'audit_id', 'version', 'expert_nom', 'confidentialite_texte',
        'cadre_mission', 'methodologie', 'synthese', 'analyse_risques',
        'introduction_plan_action', 'conclusion', 'equipe_expert',
        'equipe_audite', 'planning_reel', 'outils_utilises',
        'entreprise_contexte', 'justification_perimetre', 'methodologie_details',
        'demarche_risques', 'prev_actions', 'plan_act'
    ];

    protected $casts = [
        'equipe_expert' => 'array',
        'equipe_audite' => 'array',
        'planning_reel' => 'array',
        'outils_utilises' => 'array',
        'prev_actions' => 'array',
        'plan_act' => 'array'
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }
}