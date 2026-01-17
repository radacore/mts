<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\bioguru;
use App\Models\foto_profile;
use App\Models\data_siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class authController extends Controller
{
    public function login(Request $request)
    {
        $data = [
            'username' => $request->username,
            'password' => $request->password,
        ];
  
        if (Auth()->attempt($data)) {
            $token = Auth()->User()->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    public function loginSiswa(Request $request)
    {
        $data = [
            'username' => $request->username,
            'password' => $request->password,
            'role_id' => 4,
        ];
  
        if (Auth()->attempt($data)) {
            $token = Auth()->User()->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    public function info() 
    {
     $user = auth()->user();
     $pp=foto_profile::where('user_id', $user->id)->first();
     
     // Jika tidak ada data foto, berikan object kosong atau null (frontend akan handle)
     if(!$pp) {
        $pp = null;
     }

     $kelas = null;
     $nis_siswa = null;
     
     // Jika user adalah siswa (presumsi role_id 4)
     if($user->role_id == 4) {
         // Fix: Cari berdasarkan NIS, ATAU Nama.
         // Kasus user: Username = "ayatullah" (bukan NIS), tapi Nama di data siswa = "ayatullah".
         $siswa = data_siswa::where('nis', $user->username)
                            ->orWhere('nama', $user->username) 
                            ->orWhere('nama', $user->name)
                            ->with('kelas')
                            ->first();

         if($siswa) {
             $nis_siswa = $siswa->nis;
             if($siswa->kelas) {
                 $kelas = $siswa->kelas;
             }
         }
     }

    //  $nip=bioguru::where('user_id', $user->id)->firstOrFail();
     $role=DB::table('roles')->where('id', Auth()->User()->role_id)->first();
     return response()->json(['user' => $user,'pp'=>$pp,'role'=>$role, 'kelas'=>$kelas, 'nis'=>$nis_siswa], 200);
 
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'berhasil logout'
        ],200);
    }
}
