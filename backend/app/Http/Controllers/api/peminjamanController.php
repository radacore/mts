<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\data_katalog;
use App\Models\jumlah_pinjam;
use App\Models\jumlah_pinjam_alat;
use App\Models\informasi_terkini;
use App\Models\katalog;
use App\Models\notifikasi_user;
use App\Models\pinjam_alat;
use App\Models\pinjam_lab;
use App\Models\pinjam_lain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class peminjamanController extends Controller
{
    public function index()
    {
        $user = Auth()->User();
        // Laboran (2) & Admin (1) bisa lihat semua
        if ($user->role_id == 2 || $user->role_id == 1) {
            $data = pinjam_lab::with(['kelas','katalog','User.bioguru'])->latest()->get();
        } else {
            // Guru/Siswa hanya lihat punya sendiri
            $data = pinjam_lab::with(['kelas','katalog','User.bioguru'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return response()->json($data);
    }
    public function pinjamLabPost(Request $request)
    {
        $request->validate([
            'kelas_id'=>'required',
            'topik_id'=>'required',
            'tgl'=>'required',
            'jam'=>'required',
            'jam_selesai'=>'required',
            'pekan'=>'required',
        ]);

        $isClosed = informasi_terkini::where('status', 'aktif')
            ->where('tipe', 'penutupan_lab')
            ->where(function ($q) {
                $q->whereNull('mulai_at')->orWhere('mulai_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('selesai_at')->orWhere('selesai_at', '>=', now());
            })
            ->exists();

        if ($isClosed) {
            return response()->json([
                'message' => 'Peminjaman lab ditutup sementara karena ada informasi kegiatan aktif.',
            ], 422);
        }

        $data=pinjam_lab::updateOrCreate(['id'=>$request->id],[
            'katalog_id'=>$request->topik_id,
            'kelas_id'=>$request->kelas_id,
            'user_id'=>Auth()->User()->id,
            'tgl'=>$request->tgl,
            'jam'=>$request->jam,
            'jam_selesai'=>$request->jam_selesai,
            'pekan'=>$request->pekan,
            'peminjam'=>Auth()->User()->name,
            'status'=>'diajukan'
        ]);
        return response()->json($data);
    }
    public function pinjamLabEdit($id)
    {
        $data=pinjam_lab::where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamLabHapus($id)
    {
        if($id){
            $hapus=pinjam_lab::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function peminjamanLab()
    {
        $data=pinjam_lab::with(['kelas','katalog','user'])->with(['user.bioguru'])->whereIn('status',['diajukan','disetujui'])->latest()->get();
        return response()->json($data);
    }
    public function peminjamanAlat()
    {
        $data=pinjam_alat::with(['kelas','katalog','user'])->with(['user.bioguru'])->whereIn('status',['diajukan','disetujui','dikembalikan'])->latest()->get();
        return response()->json($data);
    }
    public function peminjamanLabProses($id,$data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman lab.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        if($id){
            $proses=pinjam_lab::find($id);
            $proses->update([
                'status'=> $data
            ]);

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Lab',
                    'pesan' => 'Pengajuan peminjaman lab Anda telah ' . $data . '.',
                    'tipe' => 'pinjam_lab_status',
                    'tautan' => '/pinjam-lab',
                    'meta' => [
                        'pinjam_lab_id' => $proses->id,
                        'status' => $data,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function peminjamanAlatProses($id,$data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman alat.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak', 'dikembalikan'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        if($id){
            $proses=pinjam_alat::find($id);
            $proses->update([
                'status'=> $data
            ]);

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Alat',
                    'pesan' => 'Pengajuan peminjaman alat Anda telah ' . $data . '.',
                    'tipe' => 'pinjam_alat_status',
                    'tautan' => '/pinjam-alat',
                    'meta' => [
                        'pinjam_alat_id' => $proses->id,
                        'status' => $data,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function filterTopik($id,$plid)
    {
        $data2=data_katalog::with(['katalog','inventaris','jumlah_pinjam'])->where('katalog_id',$id)
        ->get();
        $pus=[];
        $cek=DB::table('jumlah_pinjams as jml')->leftJoin('data_katalogs as kat','jml.data_katalog_id','=','kat.id')
                ->select('jml.*','kat.katalog_id')
                ->where('kat.katalog_id', $id)
                ->where('jml.pinjam_lab_id', $plid)
                ->first();
        if(empty($cek->id)){
            foreach($data2 as $row){
                $pus[] = [
                    'data_katalog_id' => $row->id,
                    'pinjam_lab_id' => $plid,
                    'minta' => 0,
                    'diberi' => 0,
                ];
            }
            jumlah_pinjam::insert($pus);
        }
        $data=DB::table('data_katalogs as dakat')
                ->leftJoin('inventaris as inv','dakat.inventaris_id','=','inv.id')
                ->leftJoin('jumlah_pinjams as jp','dakat.id','=','jp.data_katalog_id')
                ->select('dakat.*','inv.nabar','inv.jml','inv.noreg','jp.minta','jp.diberi','jp.id as jpid')
                ->where('dakat.katalog_id',$id)
                ->where('jp.pinjam_lab_id',$plid)
                ->get();
        return response()->json($data);
    }
    public function filterTopikAlat($id,$paid)
    {
        $data2=data_katalog::with(['katalog','inventaris','jumlah_pinjam_alat'])->where('katalog_id',$id)->get();
        $pus=[];
        $cek=DB::table('jumlah_pinjam_alats as jml')->leftJoin('data_katalogs as kat','jml.data_katalog_id','=','kat.id')
                ->select('jml.*','kat.katalog_id')
                ->where('kat.katalog_id', $id)
                ->where('jml.pinjam_alat_id', $paid)
                ->first();
        if(empty($cek->id)){
            foreach($data2 as $row){
                $pus[] = [
                    'data_katalog_id' => $row->id,
                    'pinjam_alat_id' => $paid,
                    'minta' => 0,
                    'diberi' => 0,
                ];
            }
            jumlah_pinjam_alat::insert($pus);
        }
        $data=DB::table('data_katalogs as dakat')
        ->leftJoin('inventaris as inv','dakat.inventaris_id','=','inv.id')
        ->leftJoin('jumlah_pinjam_alats as jp','dakat.id','=','jp.data_katalog_id')
        ->select('dakat.*','inv.nabar','inv.jml','inv.noreg','jp.minta','jp.diberi','jp.id as jpid')
        ->where('dakat.katalog_id',$id)
        ->where('jp.pinjam_alat_id',$paid)
        ->get();
        return response()->json($data);
    }
    public function jumlahPinjamPost(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam::find($request->id);
            $update->update([
                'minta'=>$request->minta
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamAlatPost(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam_alat::find($request->id);
            if (!$update) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $minta = (int) $request->minta;
            $stokTersedia = (int) DB::table('data_katalogs as dakat')
                ->leftJoin('inventaris as inv', 'dakat.inventaris_id', '=', 'inv.id')
                ->where('dakat.id', $update->data_katalog_id)
                ->value('inv.jml');

            if ($minta < 0) {
                return response()->json([
                    'message' => 'Jumlah diajukan tidak boleh kurang dari 0.'
                ], 422);
            }

            if ($minta > $stokTersedia) {
                return response()->json([
                    'message' => 'Jumlah diajukan tidak boleh melebihi jumlah tersedia.'
                ], 422);
            }

            $update->update([
                'minta'=>$minta
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamPost2(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam::find($request->id);
            $update->update([
                'diberi'=>$request->diberi
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamAlatPost2(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam_alat::find($request->id);
            if (!$update) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $diberi = (int) $request->diberi;
            $minta = (int) $update->minta;

            if ($diberi < 0) {
                return response()->json([
                    'message' => 'Jumlah diberikan tidak boleh kurang dari 0.'
                ], 422);
            }

            if ($diberi > $minta) {
                return response()->json([
                    'message' => 'Jumlah diberikan tidak boleh melebihi jumlah diajukan.'
                ], 422);
            }

            $update->update([
                'diberi'=>$diberi
            ]);
        }
        return response()->json($update);
    }
    public function lkpds(Request $request)
    {
        $request->validate([
            'lkpd'=>'required|mimes:,pdf,doc,docx'
        ]);
        if($request->id){
            $upload=pinjam_lab::find($request->id);
            $lkpd=$request->file('lkpd')->store('lkpd','public');
            $upload->update([
                'lkpd'=>$lkpd
            ]);
        }
        return response()->json($upload);
    }
    public function lkpdalat(Request $request)
    {
        $request->validate([
            'lkpd'=>'required|mimes:,pdf,doc,docx'
        ]);
        if($request->id){
            $upload=pinjam_alat::find($request->id);
            $lkpd=$request->file('lkpd')->store('lkpd','public');
            $upload->update([
                'lkpd'=>$lkpd
            ]);
        }
        return response()->json($upload);
    }
    public function pinjamAlat()
    {
        $user = Auth()->User();
        if ($user->role_id == 2 || $user->role_id == 1) {
             $data = pinjam_alat::with(['katalog','kelas','user.bioguru'])->latest()->get();
        } else {
             $data = pinjam_alat::with(['katalog','kelas','user.bioguru'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return response()->json($data);
    }
    public function pinjamAlatPost(Request $request)
    {
        $request->validate([
            'kelas_id'=>'required',
            'katalog_id'=>'required',
            'tgl_pakai'=>'required',
            'tgl_kembali'=>'required',
            'jam_kembali'=>'required',
            'jam_pakai'=>'required',
            'lokasi'=>'required',
            'keperluan'=>'required',
        ]);
        $data=pinjam_alat::updateOrCreate(['id'=>$request->id],[
            'kelas_id'=>$request->kelas_id,
            'katalog_id'=>$request->katalog_id,
            'tgl_pakai'=>$request->tgl_pakai,
            'user_id'=>Auth()->User()->id,
            'jam_pakai'=>$request->jam_pakai,
            'jam_kembali'=>$request->jam_kembali,
            'tgl_kembali'=>$request->tgl_kembali,
            'jam'=>$request->jam,
            'lokasi'=>$request->lokasi,
            'keperluan'=>$request->keperluan,
            'status'=>'diajukan',
        ]);
        return response()->json($data);
    }
    public function pinjamAlatEdit($id)
    {
        $data=pinjam_alat::where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamAlatHapus($id)
    {
        if($id){
            $hapus=pinjam_alat::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function pinjamLain()
    {
        $user = Auth()->User();
        if ($user->role_id == 2 || $user->role_id == 1) {
            $data = pinjam_lain::with('user')->latest()->get();
        } else {
            $data = pinjam_lain::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return response()->json($data);
    }
    public function pinjamLainPost(Request $request)
    {
        $request->validate([
            'tgl'=>'required',
            'mulai'=>'required',
            'selesai'=>'required',
            'kegiatan'=>'required',
        ]);
        $data=pinjam_lain::updateOrCreate(['id'=>$request->id],[
            'tgl'=>$request->tgl,
            'mulai'=>$request->mulai,
            'selesai'=>$request->selesai,
            'kegiatan'=>$request->kegiatan,
            'status'=>'diajukan',
            'user_id'=>Auth()->User()->id
        ]);
        return response()->json($data);
    }
    public function pinjamLainEdit($id)
    {
        $data=pinjam_lain::where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamLainHapus($id)
    {
        if($id){
            $hapus=pinjam_lain::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function peminjamanLain()
    {
        $data=pinjam_lain::with('user')->whereIn('status',['diajukan','disetujui'])->latest()->get();
        return response()->json($data);
    }
    public function pinjamLainClient()
    {
        $data=pinjam_lain::where('status','disetujui')->latest()->get();
        return response()->json($data);
    }
    public function peminjamanLainProses($id, $data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman kegiatan lain.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        if($id){
            $proses=pinjam_lain::find($id);
            $proses->update([
                'status'=> $data
            ]);

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Kegiatan Lain',
                    'pesan' => 'Pengajuan kegiatan lain Anda telah ' . $data . '.',
                    'tipe' => 'pinjam_lain_status',
                    'tautan' => '/pinjam-lain',
                    'meta' => [
                        'pinjam_lain_id' => $proses->id,
                        'status' => $data,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function pinjamLabCopy($id)
    {
        $cek=pinjam_lab::where('id', $id)->first();
        $jml=jumlah_pinjam::where('pinjam_lab_id',$id)->get();
        $datas=[];
        $data=pinjam_lab::create([
            'kelas_id'=>$cek->kelas_id,
            'katalog_id'=>$cek->katalog_id,
            'tgl'=>$cek->tgl,
            'peminjam'=>$cek->peminjam,
            'pekan'=>$cek->pekan,
            'jam'=>$cek->jam,
            'status'=>'diajukan',
            'jam_selesai'=>$cek->jam_selesai,
            'user_id'=>Auth()->User()->id,

        ]);
        foreach($jml as $row){
            $datas[]=[
                'data_katalog_id' => $row->data_katalog_id,
                'pinjam_lab_id' => $data->id,
                'minta' => $row->minta,
                'diberi' => 0,
            ];
        }
        jumlah_pinjam::insert($datas);
        return response()->json($data);
    }
    public function pinjamLainCopy($id)
    {
        $cek = pinjam_lain::where('id', $id)->first();

        if (!$cek) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $data = pinjam_lain::create([
            'user_id' => Auth()->User()->id,
            'tgl' => $cek->tgl,
            'mulai' => $cek->mulai,
            'selesai' => $cek->selesai,
            'kegiatan' => $cek->kegiatan,
            'status' => 'diajukan',
        ]);

        return response()->json($data);
    }
    public function pinjamAlatCopy($id)
    {
        $cek=pinjam_alat::where('id', $id)->first();
        $jml=jumlah_pinjam_alat::where('pinjam_alat_id', $id)->get();
        $datas=[];
        $data=pinjam_alat::create([
            'kelas_id'=>$cek->kelas_id,
            'katalog_id'=>$cek->katalog_id,
            'user_id'=>Auth()->User()->id,
            'tgl_pakai'=>$cek->tgl_pakai,
            'tgl_kembali'=>$cek->tgl_kembali,
            'jam_pakai'=>$cek->jam_pakai,
            'jam_kembali'=>$cek->jam_kembali,
            'jam'=>$cek->jam,
            'lokasi'=>$cek->lokasi,
            'keperluan'=>$cek->keperluan,
            'status'=>'diajukan',
        ]);
        foreach($jml as $row){
            $datas[]=[
                'data_katalog_id'=>$row->data_katalog_id,
                'pinjam_alat_id'=>$data->id,
                'minta' => $row->minta,
                'diberi' => 0,
            ];
        }
        jumlah_pinjam_alat::insert($datas);
        return response()->json($data);
    }

}
