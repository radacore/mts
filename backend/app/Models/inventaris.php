<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventaris extends Model
{
    protected $table ="inventaris";
    public $fillable = ['noreg','katalog','nabar','spec','satuan','vol','merek','tipe','produsen','asal','thn_masuk','thn_pakai','jml','kondisi','lokasi','foto','ket','konbaik','konrusak','jenis_barang','stok_minimum'];

    public function data_katalog()
    {
    	return $this->hasOne('App\Models\data_katalog');
    }

    public function mutations()
    {
        return $this->hasMany('App\Models\inventaris_mutation', 'inventaris_id', 'id');
    }
}
