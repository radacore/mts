<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulLkpd extends Model
{
    // Table name (migration creates `modul_lkpd` singular)
    protected $table = 'modul_lkpd';
    protected $fillable = [
        'judul', 'file_path', 'file_name', 'mime_type', 'uploaded_by'
    ];

    /**
     * Relasi: modul di-upload oleh user (laboran/guru)
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Accessor: ekstensi file (pdf, docx, dll)
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Accessor: nama uploader (untuk frontend)
     */
    public function getUploaderNameAttribute()
    {
        return $this->uploader ? $this->uploader->name : 'Laboran';
    }

    /**
     * Accessor: URL download publik
     */
    public function getDownloadUrlAttribute()
    {
        return url('storage/' . $this->file_path);
    }
}