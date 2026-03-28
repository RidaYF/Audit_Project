<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function controle()
    {
        return $this->belongsTo(Controle::class, 'controle_id');
    }
}