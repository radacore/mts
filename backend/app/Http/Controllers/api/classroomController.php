<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\absensi;
use App\Models\classroom;
use App\Models\data_absen;
use App\Models\data_tugas;
use App\Models\materi_ajar;
use App\Models\penugasan;
use App\Models\ModulLkpd; // ✅ tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class classroomController extends Controller
{
    public function index()
    {
        $data = classroom::with(['katalog', 'kelas'])->where('user_id', Auth()->User()->id)->latest()->get();
        return response()->json($data);
    }

    public function classroomPost(Request $request)
    {
        $request->validate([
            'katalog_id' => 'required',
            'kelas_id' => 'required',
        ]);
        $data = classroom::updateOrCreate(['id' => $request->id], [
            'katalog_id' => $request->katalog_id,
            'kelas_id' => $request->kelas_id,
            'user_id' => Auth()->User()->id,
        ]);
        return response()->json($data);
    }

    public function classroomEdit($id)
    {
        $data = classroom::where('id', $id)->first();
        return response()->json($data);
    }

    public function classroomHapus($id)
    {
        if ($id) {
            $hapus = classroom::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }

    // ✅ DIPERBARUI: Ambil materi + modul + link_tambahan
    public function materi($id)
    {
        $data = materi_ajar::where('classroom_id', $id)
            ->with(['modul:id,judul,file_path,file_name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($m) {
                $extension = null;
                $file_name = null;
                $file_path = null;

                if ($m->modul) {
                    $file_name = $m->modul->file_name;
                    $file_path = $m->modul->file_path;
                    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                } elseif ($m->file) {
                    $file_name = basename($m->file);
                    $file_path = $m->file;
                    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                }

                return [
                    'id' => $m->id,
                    'judul' => $m->judul,
                    'des' => $m->des,
                    'modul_id' => $m->modul_id,
                    'modul_judul' => $m->modul ? $m->modul->judul : null,
                    'modul_file_name' => $file_name,
                    'modul_file_path' => $file_path,
                    'modul_extension' => $extension,
                    'link_tambahan' => $m->link_tambahan,
                    'created_at' => $m->created_at,
                ];
            });

        return response()->json($data);
    }

    // ✅ DIPERBARUI SESUAI PERMINTAAN:  
// Modul dan file BENAR-BENAR OPSIONAL — hanya judul wajib.
public function materiPost(Request $request)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'class_id' => 'required|exists:classrooms,id',
        'file' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480',
        'modul_id' => 'nullable|exists:modul_lkpd,id',
        'link_tambahan' => 'nullable|url',
    ]);

    // Normalisasi modul_id → null jika kosong
    $modulId = $request->modul_id ? (int) $request->modul_id : null;

    // ✅ HAPUS VALIDASI BISNIS: modul_id/file tidak wajib lagi

    $file_path = $request->file ? $request->file('file')->store('modul', 'public') : null;

    $data = materi_ajar::updateOrCreate(
        ['id' => $request->id],
        [
            'judul' => $request->judul,
            'des' => $request->des,
            'file' => $file_path,
            'modul_id' => $modulId,
            'link_tambahan' => $request->link_tambahan,
            'classroom_id' => $request->class_id,
        ]
    );

    return response()->json($data);
}

    // ✅ DIPERBARUI: Edit materi
    public function materiEdit($id)
    {
        $m = materi_ajar::with('modul')->findOrFail($id);

        $file_name = null;
        $file_path = null;
        $extension = null;

        if ($m->modul) {
            $file_name = $m->modul->file_name;
            $file_path = $m->modul->file_path;
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        } elseif ($m->file) {
            $file_name = basename($m->file);
            $file_path = $m->file;
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        }

        return response()->json([
            'id' => $m->id,
            'judul' => $m->judul,
            'des' => $m->des,
            'modul_id' => $m->modul_id,
            'modul_judul' => $m->modul ? $m->modul->judul : null,
            'modul_file_name' => $file_name,
            'modul_file_path' => $file_path,
            'modul_extension' => $extension,
            'link_tambahan' => $m->link_tambahan,
        ]);
    }

    public function materiHapus($id)
    {
        $cek = materi_ajar::where('id', $id)->first();
        if ($id) {
            if (!empty($cek->file)) {
                File::delete('storage/' . $cek->file);
            }
            $hapus = materi_ajar::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }

    public function classroomCek($id)
    {
        $data = classroom::with('katalog')->where('id', $id)->first();
        return response()->json($data);
    }

    // ... (method penugasan, absensi, dll — TIDAK DIUBAH) ...
    // PENUGASAN (CRUD)
    public function penugasan($id)
    {
        $data = penugasan::where('classroom_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($data);
    }

    public function penugasanPost(Request $request)
    {
        $request->validate([
            'jt' => 'required|string|max:255',
            'class_id' => 'required|exists:classrooms,id',
            'soal' => 'nullable|string',
            'tipe_esay' => 'boolean',
            'tipe_upload' => 'boolean',
            'tipe_link' => 'boolean',
        ]);

        $data = penugasan::updateOrCreate(
            ['id' => $request->tugas_id],
            [
                'jt' => $request->jt,
                'soal' => $request->soal,
                'classroom_id' => $request->class_id,
                'tipe_esay' => $request->tipe_esay ?? true,
                'tipe_upload' => $request->tipe_upload ?? true,
                'tipe_link' => $request->tipe_link ?? true,
            ]
        );

        return response()->json($data);
    }

    public function penugasanEdit($id)
    {
        $data = penugasan::findOrFail($id);
        return response()->json($data);
    }

    public function penugasanHapus($id)
    {
        $cek = penugasan::where('id', $id)->first();
        if ($id && $cek) {
            $hapus = penugasan::find($id);
            $hapus->delete();
        }
        return response()->json($cek);
    }

    // ABSENSI (CRUD)
    public function absensi($id)
    {
        $data = absensi::where('classroom_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($data);
    }

    public function absensiPost(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classrooms,id',
            'tgl_absen' => 'nullable|date',
            'jam_buka' => 'nullable',
            'jam_tutup' => 'nullable',
        ]);

        $data = absensi::updateOrCreate(
            ['id' => $request->absen_id],
            [
                'classroom_id' => $request->class_id,
                'tgl' => $request->tgl_absen ?? null,
                'tgl_absen' => $request->tgl_absen ?? null,
                'jam_buka' => $request->jam_buka ?? null,
                'jam_tutup' => $request->jam_tutup ?? null,
                'status' => $request->status ?? 1,
            ]
        );

        return response()->json($data);
    }

    public function absensiHapus($id)
    {
        $cek = absensi::where('id', $id)->first();
        if ($id && $cek) {
            $hapus = absensi::find($id);
            $hapus->delete();
        }
        return response()->json($cek);
    }

    public function absensiStatus($id)
    {
        $a = absensi::findOrFail($id);
        $a->status = $a->status ? 0 : 1;
        $a->save();
        return response()->json($a);
    }

    // DATA ABSEN — list semua data_absen untuk absensi tertentu
    public function dataAbsen($id)
    {
        $data = data_absen::where('absensi_id', $id)
            ->with(['user', 'user.foto_profile'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($data);
    }

    // ✅ DATA TUGAS — Jawaban Essay (untuk guru lihat)
    public function dataTugasEsay($id)
    {
        $data = data_tugas::where('penugasan_id', $id)
            ->with(['user', 'user.foto_profile'])
            ->whereNotNull('esay')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($data);
    }

    // ✅ DATA TUGAS — File Upload (untuk guru lihat)
    public function dataTugasFile($id)
    {
        $data = data_tugas::where('penugasan_id', $id)
            ->with(['user', 'user.foto_profile'])
            ->whereNotNull('file')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user' => $item->user,
                    'nilai' => $item->nilai,
                    'file' => $item->file,
                    'file_name' => $item->file_name,  // ✅ Include nama file asli
                    'file_size' => $item->file_size,  // ✅ Include ukuran file
                    'created_at' => $item->created_at,
                ];
            });
        return response()->json($data);
    }

    // ✅ DATA TUGAS — Link/Tautan (untuk guru lihat)
    public function dataTugasTautan($id)
    {
        $data = data_tugas::where('penugasan_id', $id)
            ->with(['user', 'user.foto_profile'])
            ->whereNotNull('tautan')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($data);
    }

    // ✅ DATA TUGAS — Update nilai
    public function dataTugasNilai($id, $nilai)
    {
        $data = data_tugas::findOrFail($id);
        $data->nilai = $nilai;
        $data->save();
        return response()->json($data);
    }
}