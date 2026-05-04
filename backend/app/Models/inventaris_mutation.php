<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventaris_mutation extends Model
{
    use HasFactory;

    protected $table = 'inventaris_mutations';

    public $fillable = [
        'inventaris_id',
        'tahun',
        'qty',
        'jenis',
        'kondisi_asal',
        'keterangan',
        'created_by',
    ];
}
