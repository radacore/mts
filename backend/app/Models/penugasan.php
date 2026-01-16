<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penugasan extends Model
{
    protected $table ="penugasans";
    public $fillable = ['classroom_id','jt','soal'];

    public function classroom()
    {
    	return $this->belongsTo(classroom::class,'classroom_id','id');
    }
    public function data_tugas()
    {
    	return $this->hasOne('App\Models\data_tugas');
    }
}
