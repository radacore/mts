<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class classroom extends Model
{
    protected $table ="classrooms";
    public $fillable = ['user_id','kelas_id','katalog_id'];

    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
    public function kelas()
    {
    	return $this->belongsTo(kelas::class,'kelas_id','id');
    }
    public function katalog()
    {
    	return $this->belongsTo(katalog::class,'katalog_id','id');
    }
    public function materi_ajar()
    {
    	return $this->hasOne('App\Models\materi_ajar');
    }
    public function penugasan()
    {
    	return $this->hasOne('App\Models\penugasan');
    }
    public function absensi()
    {
    	return $this->hasOne('App\Models\absensi');
    }
}
