<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_siswa extends Model
{
    protected $table ="data_siswas";
    public $fillable = ['kelas_id','nis','nama','ket','email'];

    public function kelas()
    {
    	return $this->belongsTo(kelas::class,'kelas_id','id');
    }
}
