<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jumlah_pinjam extends Model
{
    protected $table ="jumlah_pinjams";
    public $fillable = ['data_katalog_id','pinjam_lab_id','minta','diberi'];

    public function data_katalog()
    {
    	return $this->belongsTo(data_katalog::class,'data_katalog_id','id');
    }

    public function pinjam_lab()
    {
    	return $this->belongsTo(pinjam_lab::class,'pinjam_lab_id','id');
    }

}
