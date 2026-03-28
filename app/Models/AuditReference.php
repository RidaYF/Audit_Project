<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditReference extends Model
{
    use HasFactory;

    protected $table = 'audit_references';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'audit_id',
        'reference_id',
        'objectif_specifique',
        'ponderation'
    ];
    
    protected $casts = [
        'ponderation' => 'integer'
    ];
    
    /**
     * Relation avec l'audit
     */
    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }
    
    /**
     * Relation avec la référence
     */
    public function reference()
    {
        return $this->belongsTo(Reference::class, 'reference_id');
    }
}