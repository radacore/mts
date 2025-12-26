<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_katalog extends Model
{
    protected $table ="data_katalogs";
    public $fillable = ['katalog_id','inventaris_id'];

    public function katalog()
    {
    	return $this->belongsTo(katalog::class,'katalog_id','id');
    }
    public function inventaris()
    {
    	return $this->belongsTo(inventaris::class,'inventaris_id','id');
    }
    public function jumlah_pinjam()
    {
    	return $this->hasOne('App\Models\jumlah_pinjam');
    }
    public function jumlah_pinjam_alat()
    {
    	return $this->hasOne('App\Models\jumlah_pinjam_alat');
    }

}
