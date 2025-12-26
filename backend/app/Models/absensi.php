<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absensi extends Model
{
    protected $table ="absensis";
    public $fillable = ['classroom_id','tgl','status','tgl_absen','jam_buka','jam_tutup'];

    public function classroom()
    {
    	return $this->belongsTo(classroom::class,'classroom_id','id');
    }
    public function data_absen()
    {
    	return $this->hasOne('App\Models\data_absen');
    }
}
