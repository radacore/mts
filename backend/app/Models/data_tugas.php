<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_tugas extends Model
{
    protected $table ="data_tugas";
    public $fillable = ['penugasan_id','user_id','nilai','file','esay','tautan'];

    public function penugasan()
    {
    	return $this->belongsTo(penugasan::class,'penugasan_id','id');
    }
    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
}
