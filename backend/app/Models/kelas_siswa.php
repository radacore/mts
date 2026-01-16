<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelas_siswa extends Model
{
    protected $table ="kelas_siswas";
    public $fillable = ['user_id','kelas_id'];

    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
    public function kelas()
    {
    	return $this->belongsTo(User::class,'kelas_id','id');
    }
}
