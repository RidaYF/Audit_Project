<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'code', 'nom', 'description', 'type_norme', 'version',
        'date_publication', 'url_officielle', 'ordre_affichage', 'is_active'
    ];

    protected $casts = [
        'date_publication' => 'date',
        'is_active' => 'boolean'
    ];

    public function clauses()
    {
        return $this->hasMany(Clause::class, 'reference_id');
    }

    public function audits()
    {
        return $this->belongsToMany(Audit::class, 'audit_references')
                    ->withPivot('objectif_specifique', 'ponderation')
                    ->withTimestamps();
    }
}