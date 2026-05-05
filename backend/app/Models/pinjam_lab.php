<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pinjam_lab extends Model
{
    protected $table ="pinjam_labs";
    public $fillable = ['kelas_id','katalog_id','tgl','peminjam','pekan','jam','jam_selesai','status','alasan_penolakan','user_id','lkpd'];

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
    public function jumlah_pinjam()
    {
    	return $this->hasOne('App\Models\jumlah_pinjam');
    }

    public function modulLkpd()
    {
        return $this->belongsToMany(ModulLkpd::class, 'pinjam_lab_modul_lkpd', 'pinjam_lab_id', 'modul_lkpd_id')
            ->withTimestamps();
    }
}
