<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class silde extends Model
{
    protected $table ="sildes";
    public $fillable = ['judul','ket','gambar','status'];
}
