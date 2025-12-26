<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public function foto_profile()
    {
    	return $this->hasOne('App\Models\foto_profile');
    }
    public function kelas_siswa()
    {
    	return $this->hasOne('App\Models\kelas_siswa');
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
    public function data_absen()
    {
    	return $this->hasOne('App\Models\data_absen');
    }
    public function data_tugas()
    {
    	return $this->hasOne('App\Models\data_tugas');
    }
    public function pinjam_lain()
    {
    	return $this->hasOne('App\Models\pinjam_lain');
    }
    public function bioguru()
    {
    	return $this->hasOne('App\Models\bioguru');
    }
   
}
