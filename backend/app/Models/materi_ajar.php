<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModulLkpd;

class materi_ajar extends Model
{
    protected $table ="materi_ajars";
    public $fillable = ['classroom_id','judul','des','file','modul_id','link_tambahan'];

    public function classroom()
    {
    	return $this->belongsTo(classroom::class,'classroom_id','id');
    }

    public function modul()
    {
        return $this->belongsTo(ModulLkpd::class, 'modul_id', 'id');
    }
}
