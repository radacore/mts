<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\absensi;
use App\Models\classroom;
use App\Models\data_absen;
use App\Models\data_tugas;
use App\Models\kelas_siswa;
use App\Models\materi_ajar;
use App\Models\penugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class labsiswaController extends Controller
{
    public function index()
    {
        $cek=kelas_siswa::where("user_id", Auth()->User()->id)->first();
        $data=classroom::with(['katalog','kelas','User'])->where('kelas_id', $cek->kelas_id)->get();
        return response()->json($data);
    }
    public function absen($id)
    {
        $sekarang=Carbon::now()->format('Y-m-d');
        $jam=Carbon::now()->format('H:i:s');
        $data=DB::table('absensis')
            ->where(function($q)use($jam){
                    $q->whereTime('jam_buka','<',$jam)
                    ->whereTime('jam_tutup','>',$jam);
                })
                ->where('tgl_absen', $sekarang)
                ->where('classroom_id', $id)
                ->get();
        return response()->json([
            'data'=>$data,
            'jam'=>$jam
        ]);
    }
    public function absenPost(Request $request)
    {
        $data=data_absen::create([
            'absensi_id'=>$request->absensi_id,
            'user_id'=>Auth()->User()->id,
        ]);
        return response()->json($data);
    }
    public function absenCek($id){
        $data=data_absen::where('absensi_id', $id)->where('user_id', Auth()->User()->id)->first();
        return response()->json($data);
    }
    public function modul($id)
    {
        $data=materi_ajar::where('classroom_id', $id)->get();
        return response()->json($data);
    }
    public function tugas($id)
    {
        $data=penugasan::where('classroom_id', $id)->get();
        return response()->json($data);
    }
    public function tugasEsayPost(Request $request)
    {
        $request->validate([
            'teks'=>'required'
        ]);
        $data=data_tugas::updateOrCreate(['id'=>$request->id],[
            'penugasan_id'=>$request->penugasan_id,
            'esay'=>$request->teks,
            'user_id'=>Auth()->User()->id
        ]);
        return response()->json($data);
    }
    public function tugasEsay($id)
    {
        $data=data_tugas::where('penugasan_id', $id)->where('user_id', Auth()->User()->id)->where('esay','<>',Null)->get();
        return response()->json($data);
    }
    public function tugasEsayEdit($id){
        $data=data_tugas::where('id', $id)->first();
        return response()->json($data);
    }
    public function tugasEsayHapus($id)
    {
        if($id){
            $hapus=data_tugas::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function tugasUpload($id)
    {
        $data=data_tugas::where('penugasan_id', $id)->where('user_id', Auth()->User()->id)->where('file','<>',Null)->get();
        return response()->json($data);
    }
    public function tugasUploadPost(Request $request)
    {
        $request->validate([
            'file'=>'required|mimes:jpg,jpeg,png'
        ]);
        $file=$request->file('file')->store('penugasan','public');
        $data=data_tugas::create([
            'penugasan_id'=>$request->penugasan_id,
            'file'=>$file,
            'user_id'=>Auth()->User()->id
        ]);
        return response()->json($data);
    }
    public function tugasUploadHapus($id)
    {
        $cek=data_tugas::where('id', $id)->first();
        if($id){
            $hapus=data_tugas::find($id);
            if(!empty($cek->file)){
                File::delete('storage/'.$cek->file);
            }
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function tugasTautan($id)
    {
        $data=data_tugas::where("penugasan_id", $id)->where('user_id', Auth()->User()->id)->where('tautan','<>',Null)->get();
        return response()->json($data);
    }
    public function tugasTautanPost(Request $request)
    {
        $request->validate([
            'tautan'=>'required'
        ]);
        $data=data_tugas::create([
            'penugasan_id'=>$request->penugasan_id,
            'tautan'=>$request->tautan,
            'user_id'=>Auth()->User()->id
        ]);
        return response()->json($data);
    }
    public function tugasTautanHapus($id)
    {
        if($id){
            $hapus=data_tugas::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
}
