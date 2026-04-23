<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\inventaris;
use App\Models\pinjam_alat;
use App\Models\pinjam_lab;
use App\Models\pinjam_lain;
use App\Models\silde;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class dashboardController extends Controller
{
    public function statistik()
    {
        $batasStokMenipis = 5;
        $inv = (int) inventaris::sum('jml');
        $kondisi = (int) inventaris::sum('konbaik');
        $rusak = (int) inventaris::sum('konrusak');
        $stokHabis = (int) inventaris::where('jml', '<=', 0)->count();
        $stokMenipis = (int) inventaris::where('jml', '>', 0)->where('jml', '<=', $batasStokMenipis)->count();
        $pinjamLabPending = (int) pinjam_lab::where('status', 'diajukan')->count();
        $pinjamAlatPending = (int) pinjam_alat::where('status', 'diajukan')->count();
        $pinjamLainPending = (int) pinjam_lain::where('status', 'diajukan')->count();

        return response()->json([
            'inv'=>$inv,
            'kondisi'=>$kondisi,
            'rusak'=>$rusak,
            'stok_menipis' => $stokMenipis,
            'stok_habis' => $stokHabis,
            'stok_kritis' => $stokMenipis + $stokHabis,
            'batas_stok_menipis' => $batasStokMenipis,
            'pinjam_lab_pending' => $pinjamLabPending,
            'pinjam_alat_pending' => $pinjamAlatPending,
            'pinjam_lain_pending' => $pinjamLainPending,
        ]);
    }
    public function jadwalLab()
    {
        // $data=pinjam_lab::with(['kelas','katalog','User'])->with(['User.foto_profile'])->where('status','disetujui')->orderBy('tgl','desc')->get();
        $data=DB::table('pinjam_labs as lab')
            ->leftJoin('kelas','lab.kelas_id','=','kelas.id')
            ->leftJoin('katalogs as topik','lab.katalog_id','=','topik.id')
            ->leftJoin('foto_profiles as foto','lab.user_id','=','foto.user_id')
            ->select('lab.*','kelas.kelas','topik.topik','foto.foto')
            ->where('lab.status','disetujui')
            ->orderBy('lab.tgl','desc')
            ->get();
        return response()->json($data);
    }
    public function dataSlide()
    {
        $data=silde::latest()->get();
        return response()->json($data);
    }
    public function dataSlidePost(Request $request)
    {
        $request->validate([
            'judul'=>'required',
            'ket'=>'required',
        ]);
        if($request->file('gambar')!=Null){
            $gambar=$request->file('gambar')->store('slide','public');
            $data=silde::updateOrCreate(['id'=>$request->id],[
                'judul'=>$request->judul,
                'ket'=>$request->ket,
                'status'=>'on',
                'gambar'=>$gambar
            ]);
        }else{
            $data=silde::updateOrCreate(['id'=>$request->id],[
                'judul'=>$request->judul,
                'ket'=>$request->ket,
                'status'=>'on',
            ]);
        }
           
        return response()->json($data);
        
    }
    public function dataSlideEdit($id)
    {
        $data=silde::where('id', $id)->first();
        return response()->json($data);
    }
    public function dataSlideHapus($id)
    {
        $cek=silde::where('id', $id)->first();
        if($id){
            $hapus=silde::find($id);
            if(!empty($cek->gambar)){
                File::delete('storage/'.$cek->file);
            }
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function dataSlideStatus($id,$stat)
    {
        if($id){
            $update=silde::find($id);
            $update->update([
                'status'=>$stat
            ]);
        }
        return response()->json($update);
    }
    public function guru()
    {
        $data=User::where('role_id', 3)->with(['bioguru','foto_profile'])->get();
        return response()->json($data);
    }
}
