<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifikasi_user extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_users';

    protected $casts = [
        'meta' => 'array',
        'dibaca' => 'boolean',
    ];

    public $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'tautan',
        'meta',
        'dibaca',
    ];
}
