<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'nom', 'secteur', 'taille', 'image_logo', 'systemes_cles',
        'localisation', 'contexte_metier', 'departements', 'actifs_critiques',
        'chiffre_affaires', 'effectif', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effectif' => 'integer'
    ];

    public function audits()
    {
        return $this->hasMany(Audit::class, 'entreprise_id');
    }
}