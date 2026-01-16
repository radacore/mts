<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelas extends Model
{
    protected $table ="kelas";
    public $fillable = ['kelas'];

    public function kelas_siswa()
    {
    	return $this->hasOne('App\Models\kelas_siswa');
    }
    public function pinjam_lab()
    {
    	return $this->hasOne('App\Models\pinjam_lab');
    }
    public function classroom()
    {
    	return $this->hasOne('App\Models\classroom');
    }
    public function pinjam_alat()
    {
    	return $this->hasOne('App\Models\pinjam_alat');
    }
    public function data_siswa()
    {
    	return $this->hasOne('App\Models\data_siswa');
    }
}
