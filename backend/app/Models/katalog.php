<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class katalog extends Model
{
    protected $table ="katalogs";
    public $fillable = ['topik'];

    public function data_katalog()
    {
    	return $this->hasOne('App\Models\data_katalog');
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
   
}
