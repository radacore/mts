<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pinjam_alat extends Model
{
    protected $table ="pinjam_alats";
    public $fillable = ['kelas_id','katalog_id','user_id','tgl_pakai','tgl_kembali','jam_pakai','jam_kembali','jam','lokasi','keperluan','status','lkpd'];

    public function kelas()
    {
    	return $this->belongsTo(kelas::class,'kelas_id','id');
    }
    public function katalog()
    {
    	return $this->belongsTo(katalog::class,'katalog_id','id');
    }
    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
    public function jumlah_pinjam_alat()
    {
    	return $this->hasOne('App\Models\jumlah_pinjam_alat');
    }

}
