<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class foto_profile extends Model
{
    protected $table ="foto_profiles";
    public $fillable = ['user_id','foto'];

    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
    
}
