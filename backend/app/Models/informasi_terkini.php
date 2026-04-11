<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class informasi_terkini extends Model
{
    use HasFactory;

    protected $table = 'informasi_terkini';

    public $fillable = [
        'judul',
        'isi',
        'tipe',
        'status',
        'mulai_at',
        'selesai_at',
        'dibuat_oleh',
    ];
}
