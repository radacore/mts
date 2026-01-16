<?php

namespace App\Http\Controllers\api;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\data_siswa;
use App\Models\foto_profile;
use App\Models\kelas_siswa;
use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\importSiswa;

class userController extends Controller
{
    public function super()
    {
        $data=DB::table('users')->leftJoin('roles','users.role_id','=','roles.id')
                ->whereIn('users.role_id',[1,2])
                ->select('users.*','roles.role')
                ->get();
        return response()->json($data);
    }
    public function guru()
    {
        $data=DB::table('users')->leftJoin('roles','users.role_id','=','roles.id')
                ->where('users.role_id',3)
                ->select('users.*','roles.role')
                ->get();
        return response()->json($data);
    }
    public function siswa()
    {
        $data=DB::table('users')->leftJoin('roles','users.role_id','=','roles.id')
                ->leftJoin('kelas_siswas as kelas','users.id','=','kelas.user_id')
                ->leftJoin('kelas as rom','kelas.kelas_id','=','rom.id')
                ->where('users.role_id',4)
                ->select('users.*','roles.role','rom.kelas')
                ->get();
        return response()->json($data);
    }
    public function superPost(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'username'=>'required',
            'email'=>'required',
            'role_id'=>'required',
            'password'=>'required',
        ]);
        $data=User::updateOrCreate(['id'=>$request->id],[
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'role_id'=>$request->role_id,
            'password'=>bcrypt($request->password),
        ]);
        $cek=foto_profile::where('user_id', $request->id)->first();
        if(empty($cek->id)){
            foto_profile::create([
                'user_id'=>$data->id,
                'foto'=>'foto/user.jpg'
            ]);
        }else{
            return response()->json($data);
        }

    }
    public function siswaPost(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'username'=>'required',
            'email'=>'required',
            'rombel_id'=>'required',
            'password'=>'required',
        ]);
        $data=User::updateOrCreate(['id'=>$request->id],[
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'role_id'=>4,
            'password'=>bcrypt($request->password),
        ]);
        $kelas=kelas_siswa::where('user_id', $request->id)->first();
        $cek=foto_profile::where('user_id', $request->id)->first();
        if(empty($kelas->id)){
            $kls=kelas_siswa::create([
                'user_id'=>$data->id,
                'kelas_id'=>$request->rombel_id
            ]);
        }
        if(empty($cek->id)){
            $pp=foto_profile::create([
                'user_id'=>$data->id,
                'foto'=>'foto/user.jpg'
            ]);
        }else{

            return response()->json($data);
        }
            
        

    }
    public function guruPost(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'username'=>'required',
            'email'=>'required',
            'password'=>'required',
        ]);
        $data=User::updateOrCreate(['id'=>$request->id],[
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'role_id'=>3,
            'password'=>bcrypt($request->password),
        ]);
        $cek=foto_profile::where('user_id', $request->id)->first();
        if(empty($cek->id)){
            foto_profile::create([
                'user_id'=>$data->id,
                'foto'=>'foto/user.jpg'
            ]);
        }else{
            return response()->json($data);
        }

    }
    public function superEdit($id)
    {
        $data=User::where('id', $id)->first();
        return response()->json($data);
    }
    public function guruEdit($id)
    {
        $data=User::where('id', $id)->first();
        return response()->json($data);
    }
    public function siswaEdit($id)
    {
        $data=User::where('id', $id)->first();
        return response()->json($data);
    }
    public function superHapus($id)
    {
        if($id){
            $hapus=User::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function guruHapus($id)
    {
        if($id){
            $hapus=User::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function siswaHapus($id)
    {
        if($id){
            $hapus=User::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function role()
    {
        $data=DB::table('roles')->get();
        return response()->json($data);
    }
    public function rolePost(Request $request)
    {
        $request->validate([
            'role'=>'required',
            'id'=>'required'
        ]);
       $data=DB::table('roles')->insert([
        'id'=>$request->id,
        'role'=>$request->role,
       ]);
        return response()->json($data);
    }
    public function roleEdit($id)
    {
        $data=DB::table('roles')->where('id', $id)->first();
        return response()->json($data);
    }
    public function roleUpdate(Request $request)
    {
        $request->validate([
            'role'=>'required',
            'id'=>'required'
        ]);
        if($request->id){
            $update=role::find($request->id);
            $update->update([
                'role'=>$request->role,
            ]);
        }
        return response()->json($update);
    }
    public function roleHapus($id)
    {
        if($id){
            $hapus=role::find($id);
            $hapus->delete();
        }
        return response($hapus);
    }
    public function superole()
    {
        $data=DB::table('roles')->whereIn('id',[1,2])->get();
        return response()->json($data);
    }
    public function profiles()
    {
        $data=User::where('id', Auth()->User()->id)->first();
        $foto=foto_profile::where('user_id', Auth()->user()->id)->first();
        return response()->json([
            'data'=>$data,
            'foto'=>$foto,
        ]);
    }
    public function upload(Request $request)
    {
        $request->validate([
            'foto'=>'required|image|mimes:jpeg,png,jpg,'
        ]);
        $cek=foto_profile::where('user_id', Auth()->User()->id)->first();
        if($cek->id){
            $upload=foto_profile::find($cek->id);
            // Hapus foto lama jika ada dan bukan default
            if($upload->foto && $upload->foto != 'foto/user.jpg' && file_exists(storage_path('app/public/'.$upload->foto))){
                 unlink(storage_path('app/public/'.$upload->foto));
            }
            $foto=$request->file('foto')->store('profile','public');
            $upload->update([
                'foto'=>$foto
            ]);
        }
        return response()->json($upload);
    }
    
    public function deletePhoto()
    {
        $cek = foto_profile::where('user_id', Auth()->User()->id)->first();
        
        if ($cek) {
            // Hapus file fisik jika ada dan bukan default
            if ($cek->foto && $cek->foto != 'foto/user.jpg' && file_exists(storage_path('app/public/' . $cek->foto))) {
                unlink(storage_path('app/public/' . $cek->foto));
            }
            
            // Set kembali ke default atau null. 
            // Karena sistem menggunakan ui-avatars jika null/default, kita bisa set ke null atau path default
            // Di sini kita update recordnya agar fotonya null atau default string
            
            // Opsi 1: Delete record foto_profile completely (jika sistem menghendaki one-to-one strict)
            // Opsi 2: Update kolom foto jadi null
            
            $cek->update([
               'foto' => null 
            ]);
            
            return response()->json(['message' => 'Foto berhasil dihapus', 'status' => true]);
        }
        
        return response()->json(['message' => 'Foto tidak ditemukan', 'status' => false], 404);
    }
    public function updateProfile(Request $request)
    {
        // Password opsional, hanya divalidasi jika diisi
        $request->validate([
            'password' => 'nullable|min:6'
        ]);
        
        if($request->id){
            $update=User::find($request->id);
            
            $dataToUpdate = [
                'name'=>$request->name,
                'username'=>$request->username,
                'email'=>$request->email,
            ];

            // Hanya update password jika user mengirim password baru
            if ($request->filled('password')) {
                $dataToUpdate['password'] = bcrypt($request->password);
            }

            $update->update($dataToUpdate);
        }
        return response()->json($update);
    }
    public function importSiswa(Request $request)
    {
        $request->validate([
            'file'=>'required'
        ]);
        
        // Reset counters sebelum import
        importSiswa::resetCounters();
        
        Excel::import(new importSiswa, $request->file('file')->store('files'));
        
        // Return laporan import
        return response()->json([
            'success' => true,
            'imported' => importSiswa::$imported,
            'duplicates' => importSiswa::$duplicates,
            'total_duplicates' => count(importSiswa::$duplicates)
        ]);
    }

    public function importSiswaGet(Request $request)
    {
        $query = data_siswa::with('kelas');
        
        // Filter by kelas jika ada
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        $data = $query->get();
        return response()->json($data);
    }
    
    public function cekUser($email)
    {
        $data=User::where('email',$email)->first();
        return response()->json($data);
    }
    public function userSiswa(Request $request)
    {
        $cek=data_siswa::where('id', $request->id)->first();
        $data=User::create([
            'name'=>$cek->nama,
            'email'=>$cek->email,
            'username'=>$cek->nis,
            'password'=>bcrypt($cek->nis),
            'role_id'=>4
        ]);
        return response()->json($data);
    }
    public function userSiswaHapus($id)
    {
        if($id){
            $hapus=User::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function importSiswaHapus($id)
    {
        $dasis=data_siswa::where('id', $id)->first();
        User::where('email', $dasis->email)->delete();
        if($id){
            $hapus=data_siswa::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    
    // Multiple delete data siswa
    public function importSiswaMultipleHapus(Request $request)
    {
        $ids = $request->ids;
        $deleted = 0;
        
        foreach ($ids as $id) {
            $dasis = data_siswa::where('id', $id)->first();
            if ($dasis) {
                // Hapus user terkait
                User::where('email', $dasis->email)->delete();
                // Hapus data siswa
                $dasis->delete();
                $deleted++;
            }
        }
        
        return response()->json([
            'success' => true,
            'deleted' => $deleted
        ]);
    }
}

