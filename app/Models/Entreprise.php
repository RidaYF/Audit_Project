<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Entreprise extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    
    protected $table = 'entreprises';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

  protected $fillable = [
        'nom',
        'secteur',
        'taille',
        'image_logo',
        'systemes_cles',
        'localisation',
        'contexte_metier',
        'departements',
        'actifs_critiques',
        'chiffre_affaires',
        'effectif',
        'is_active',
    ];

  protected $casts = [
        'is_active' => 'boolean',
        'effectif' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function audits()
    {
        return $this->hasMany(Audit::class, 'entreprise_id');
    }
    
    // Scope pour les entreprises actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Accesseur pour le logo
    public function getLogoUrlAttribute()
    {
        if ($this->image_logo) {
            return \Storage::url($this->image_logo);
        }
        return null;
    }
}