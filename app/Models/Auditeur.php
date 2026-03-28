<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditeur extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'utilisateur_id', 'nom', 'prenom', 'telephone',
        'certification', 'experience_ans', 'specialites'
    ];

    protected $casts = [
        'specialites' => 'array',
        'experience_ans' => 'integer'
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function audits()
    {
        return $this->hasMany(Audit::class, 'auditeur_id');
    }
}