<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\absensi;
use App\Models\classroom;
use App\Models\data_absen;
use App\Models\data_tugas;
use App\Models\materi_ajar;
use App\Models\penugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

use function PHPUnit\Framework\returnSelf;

class classroomController extends Controller
{
    public function index()
    {
        $data=classroom::with(['katalog','kelas'])->where('user_id', Auth()->User()->id)->latest()->get();
        return response()->json($data);
    }
    public function classroomPost(Request $request)
    {
        $request->validate([
            'katalog_id'=>'required',
            'kelas_id'=>'required',
        ]);
        $data=classroom::updateOrCreate(['id'=>$request->id],[
            'katalog_id'=>$request->katalog_id,
            'kelas_id'=>$request->kelas_id,
            'user_id'=>Auth()->User()->id,
        ]);
        return response()->json($data);
    }
    public function classroomEdit($id)
    {
        $data=classroom::where('id', $id)->first();
        return response()->json($data);
    }
    public function classroomHapus($id)
    {
        if($id){
            $hapus=classroom::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function materi($id)
    {
        $data=materi_ajar::where('classroom_id', $id)->get();
        return response()->json($data);
    }
    public function materiPost(Request $request)
    {
        $request->validate([
            'judul'=>'required',
            'file'=>'required|mimes:pdf,ppt,pptx',
        ]);
        $file=$request->file('file')->store('modul','public');
        $data=materi_ajar::updateOrCreate(['id'=>$request->id],[
            'judul'=>$request->judul,
            'des'=>$request->des,
            'file'=>$file,
            'classroom_id'=> $request->class_id
        ]);
        return response()->json($data);
       
    }
    public function materiEdit($id)
    {
        $data=materi_ajar::where('id', $id)->first();
        return response()->json($data);
    }
    public function materiHapus($id)
    {
        $cek=materi_ajar::where('id', $id)->first();
        if($id){
            if(!empty($cek->file)){
                File::delete('storage/'.$cek->file);
            }
            $hapus=materi_ajar::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function classroomCek($id)
    {
        $data=classroom::with('katalog')->where('id', $id)->first();
        return response()->json($data);
    }
    public function penugasan($id)
    {
        $data=penugasan::where('classroom_id', $id)->get();
        return response()->json($data);
    }
    public function penugasanPost(Request $request)
    {
        $request->validate([
            'jt'=>'required',
            'soal'=>'required',
        ]);
        $data=penugasan::updateOrCreate(['id'=>$request->tugas_id],[
            'jt'=>$request->jt,
            'soal'=>$request->soal,
            'classroom_id'=>$request->class_id,
        ]);
        return response()->json($data);
    }
    public function penugasanEdit($id)
    {
        $data=penugasan::where('id', $id)->first();
        return response()->json($data);
    }
    public function penugasanHapus($id)
    {
        if($id){
            $hapus=penugasan::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function absensi($id)
    {
        $data=absensi::where('classroom_id', $id)->get();
        return response()->json($data);
    }
    public function absensiPost(Request $request)
    {
        $sekarang=Carbon::now()->format('Y-m-d');
        $request->validate([
            'tgl_absen'=>'required',
            'jam_buka'=>'required',
            'jam_tutup'=>'required',
        ]);
        $data=absensi::create([
            'classroom_id'=>$request->class_id,
            'status'=>'open',
            'tgl'=>$sekarang,
            'tgl_absen'=>$request->tgl_absen,
            'jam_buka'=>$request->jam_buka,
            'jam_tutup'=>$request->jam_tutup,
        ]);
        return response()->json($data);
    }
    public function absensiHapus($id)
    {
        if($id){
            $hapus=absensi::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function absensiStatus($id)
    {
        $cek=absensi::where('id', $id)->first();
        if($id){
            $update=absensi::find($id);
            if($cek->status=='close'){
                $update->update([
                    'status'=>'open'
                ]);
            }else{
                $update->update([
                    'status'=>'close'
                ]);
            }
        }
        return response()->json($update);
    }
    public function dataAbsen($id)
    {
        $data=data_absen::with(['user','user.foto_profile'])->where("absensi_id", $id)->get();
        return response()->json($data);
    }
    public function dataTugasEsay($id)
    {
        $data=data_tugas::with(['user','user.foto_profile'])->where('penugasan_id', $id)->where('file', Null)->where('tautan',Null)->get();
        return response()->json($data);
    }
    public function dataTugasNilai($id,$nilai)
    {
        if($id){
            $update=data_tugas::find($id);
            $update->update([
                'nilai'=> $nilai
            ]);
        }
        return response()->json($update);
    }
    public function dataTugasFile($id)
    {
        $data=data_tugas::with(['user','user.foto_profile'])->where('penugasan_id', $id)->where('esay', Null)->where('tautan', Null)->get();
        return response()->json($data);
    }
    public function moduls()
    {
        $data=materi_ajar::latest()->get();
        return response()->json($data);
    }
    public function dataTugasTautan($id)
    {
        $data=data_tugas::with(['user','user.foto_profile'])->where('penugasan_id', $id)->where('esay', Null)->where('file', Null)->get();
        return response()->json($data);
    }

}
