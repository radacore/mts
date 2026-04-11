<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ModulLkpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModulLkpdController extends Controller
{
    /**
     * GET /api/modul/lkpd
     * Daftar modul untuk Laboran & Guru
     */
    public function index()
    {
        $user = Auth::user();

        // Laboran/Admin: lihat semua modul agar bisa dikelola
        if ($user->role_id == 2 || $user->role_id == 1) {
            $moduls = ModulLkpd::with('uploader:id,name')
                ->orderBy('created_at', 'desc')
                ->get();
        } 
        // Guru: lihat SEMUA modul
        else if ($user->role_id == 3) {
            $moduls = ModulLkpd::with('uploader:id,name')
                ->orderBy('created_at', 'desc')
                ->get();
        } 
        // Selain itu: kosong
        else {
            $moduls = collect();
        }

        return response()->json($moduls->map(function ($modul) {
            return [
                'id' => $modul->id,
                'judul' => $modul->judul,
                'file_path' => $modul->file_path,
                'file_name' => $modul->file_name,
                'mime_type' => $modul->mime_type,
                'extension' => $modul->extension,
                'uploader_name' => $modul->uploader_name ?? 'Laboran',
                'created_at' => $modul->created_at,
            ];
        }));
    }

    /**
     * POST /api/modul/lkpd
     * Upload modul oleh Laboran
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Laboran (2) dan Guru (3) boleh upload
        if (!in_array((int) $user->role_id, [2, 3], true)) {
            return response()->json([
                'message' => 'Hanya Laboran dan Guru yang diperbolehkan mengupload modul.'
            ], 403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        // Simpan ke: storage/app/public/modul/
        $path = $file->storeAs(
            'modul',
            Str::slug($request->judul) . '_' . time() . '.' . $extension,
            'public' // disk 'public' → bisa diakses via /storage/
        );

        $modul = ModulLkpd::create([
            'judul' => $request->judul,
            'file_path' => $path,
            'file_name' => $originalName,
            'mime_type' => $mimeType,
            'uploaded_by' => $user->id,
        ]);

        return response()->json([
            'id' => $modul->id,
            'judul' => $modul->judul,
            'file_path' => $modul->file_path,
            'file_name' => $modul->file_name,
            'extension' => $modul->extension,
            'uploader_name' => $modul->uploader_name,
        ], 201);
    }

    /**
     * DELETE /api/modul/lkpd/{id}
     * Hapus modul oleh Laboran
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!in_array((int) $user->role_id, [2, 3], true)) {
            return response()->json([
                'message' => 'Hanya Laboran dan Guru yang diperbolehkan menghapus modul.'
            ], 403);
        }

        // Laboran/Admin dapat mengelola semua modul, Guru hanya modul miliknya
        if ($user->role_id == 2 || $user->role_id == 1) {
            $modul = ModulLkpd::findOrFail($id);
        } else {
            $modul = ModulLkpd::where('uploaded_by', $user->id)->findOrFail($id);
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($modul->file_path)) {
            Storage::disk('public')->delete($modul->file_path);
        }

        $modul->delete();

        return response()->noContent(); // 204
    }
}
