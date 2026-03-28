<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Controle extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'clause_id', 'code', 'titre', 'description', 'objectif',
        'categorie', 'sous_categorie', 'niveau_critique', 'type_controle',
        'ponderation_defaut', 'temps_estime', 'conseils_audit'
    ];

    protected $casts = [
        'temps_estime' => 'integer',
        'ponderation_defaut' => 'integer'
    ];

    public function clause()
    {
        return $this->belongsTo(Clause::class, 'clause_id');
    }

    public function preuves()
    {
        return $this->hasMany(Preuve::class, 'controle_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'controle_id');
    }
}