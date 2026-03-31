<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Preuve extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'preuves';
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'controle_id',
        'titre',
        'type_preuve',
        'contenu',
        'points_max',
        'ordre_affichage',
        'is_active',
        'image_path',  // Gardé pour compatibilité
        'file_path'    // Nouveau pour tous les fichiers
    ];

    protected $casts = [
        'points_max' => 'integer',
        'ordre_affichage' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec le contrôle
     */
    public function controle()
    {
        return $this->belongsTo(Controle::class, 'controle_id');
    }

    /**
     * Accesseur pour obtenir le chemin du fichier (priorité à file_path)
     */
    public function getCurrentFilePathAttribute()
    {
        return $this->file_path ?? $this->image_path;
    }

    /**
     * Vérifier si la preuve a un fichier associé
     */
    public function hasFile()
    {
        return !is_null($this->file_path) || !is_null($this->image_path);
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function isImage()
    {
        $filePath = $this->current_file_path;
        if (!$filePath) return false;
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp']);
    }

    /**
     * Obtenir le type de fichier (image, pdf, document, etc.)
     */
    public function getFileTypeAttribute()
    {
        $filePath = $this->current_file_path;
        if (!$filePath) return null;
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'])) {
            return 'image';
        } elseif (in_array($extension, ['pdf'])) {
            return 'pdf';
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return 'word';
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return 'excel';
        } elseif (in_array($extension, ['txt'])) {
            return 'text';
        } else {
            return 'document';
        }
    }

    /**
     * Obtenir l'icône correspondant au type de fichier
     */
    public function getFileIconAttribute()
    {
        return match($this->file_type) {
            'image' => '🖼️',
            'pdf' => '📄',
            'word' => '📝',
            'excel' => '📊',
            'text' => '📃',
            default => '📎',
        };
    }

    /**
     * Obtenir l'URL du fichier
     */
    public function getFileUrlAttribute()
    {
        $filePath = $this->current_file_path;
        return $filePath ? Storage::url($filePath) : null;
    }

    /**
     * Obtenir le nom du fichier
     */
    public function getFileNameAttribute()
    {
        $filePath = $this->current_file_path;
        return $filePath ? basename($filePath) : null;
    }

    /**
     * Supprimer le fichier associé (image_path ou file_path)
     */
    public function deleteFile()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
            $this->file_path = null;
        }
        
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            Storage::disk('public')->delete($this->image_path);
            $this->image_path = null;
        }
        
        $this->save();
    }
}