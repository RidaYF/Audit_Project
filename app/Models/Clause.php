<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clause extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'reference_id', 'parent_id', 'numero', 'titre', 'description',
        'niveau', 'chemin_complet', 'niveau_importance', 'ordre_affichage'
    ];

    public function reference()
    {
        return $this->belongsTo(Reference::class, 'reference_id');
    }

    public function parent()
    {
        return $this->belongsTo(Clause::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Clause::class, 'parent_id');
    }

    public function controles()
    {
        return $this->hasMany(Controle::class, 'clause_id');
    }
}