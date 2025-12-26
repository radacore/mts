<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materi_ajar extends Model
{
    protected $table ="materi_ajars";
    public $fillable = ['classroom_id','judul','des','file'];

    public function classroom()
    {
    	return $this->belongsTo(classroom::class,'classroom_id','id');
    }
}
