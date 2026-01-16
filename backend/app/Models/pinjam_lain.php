<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pinjam_lain extends Model
{
    protected $table ="pinjam_lains";
    public $fillable = ['user_id','tgl','mulai','selesai','kegiatan','status'];

    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
}
