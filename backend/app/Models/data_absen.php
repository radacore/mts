<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_absen extends Model
{
    protected $table ="data_absens";
    public $fillable = ['absensi_id','user_id'];

    public function absensi()
    {
    	return $this->belongsTo(absensi::class,'absensi_id','id');
    }
    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
}
