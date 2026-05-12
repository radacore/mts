<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jumlah_pinjam_alat extends Model
{
    protected $table ="jumlah_pinjam_alats";
    public $fillable = ['data_katalog_id','pinjam_alat_id','minta','diberi','rusak','hilang'];

    public function data_katalog()
    {
    	return $this->belongsTo(data_katalog::class,'data_katalog_id','id');
    }

    public function pinjam_alat()
    {
    	return $this->belongsTo(pinjam_alat::class,'pinjam_alat_id','id');
    }
}
