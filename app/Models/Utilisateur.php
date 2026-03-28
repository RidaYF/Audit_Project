<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'username', 'email', 'password_hash', 'role', 'niveau',
        'progression_globale', 'avatar_url', 'is_active', 'date_inscription',
        'derniere_connexion'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
        'derniere_connexion' => 'datetime',
        'is_active' => 'boolean',
        'progression_globale' => 'float'
    ];

    // Redéfinir le champ de mot de passe pour Laravel
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Redéfinir le champ email pour Laravel
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function auditeur()
    {
        return $this->hasOne(Auditeur::class, 'utilisateur_id');
    }
}