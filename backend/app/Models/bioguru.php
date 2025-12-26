<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bioguru extends Model
{
    protected $table ="biogurus";
    public $fillable = ['user_id','nip','hp'];
    
    public function User()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
}
