<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\informasi_terkini;
use Illuminate\Http\Request;

class informasiController extends Controller
{
    private const MAX_ACTIVE_INFO = 3;

    private function authorizeManage(): void
    {
        $user = auth()->user();

        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            abort(403, 'Anda tidak berhak mengelola informasi terkini.');
        }
    }

    private function activeQuery()
    {
        $now = now();

        return informasi_terkini::query()
            ->where('status', 'aktif')
            ->where(function ($q) use ($now) {
                $q->whereNull('mulai_at')->orWhere('mulai_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('selesai_at')->orWhere('selesai_at', '>=', $now);
            });
    }

    public function index()
    {
        $this->authorizeManage();

        $data = informasi_terkini::query()
            ->orderByRaw("CASE WHEN status = 'aktif' THEN 0 ELSE 1 END")
            ->latest('updated_at')
            ->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:info,peringatan,penutupan_lab',
            'status' => 'required|in:aktif,nonaktif',
            'mulai_at' => 'nullable|date',
            'selesai_at' => 'nullable|date|after_or_equal:mulai_at',
        ]);

        if ($request->status === 'aktif') {
            $activeCount = informasi_terkini::query()
                ->where('status', 'aktif')
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->count();

            if ($activeCount >= self::MAX_ACTIVE_INFO) {
                return response()->json([
                    'message' => 'Maksimal 3 informasi aktif. Nonaktifkan salah satu terlebih dahulu.',
                ], 422);
            }
        }

        $data = informasi_terkini::updateOrCreate(
            ['id' => $request->id],
            [
                'judul' => $request->judul,
                'isi' => $request->isi,
                'tipe' => $request->tipe,
                'status' => $request->status,
                'mulai_at' => $request->mulai_at,
                'selesai_at' => $request->selesai_at,
                'dibuat_oleh' => auth()->id(),
            ]
        );

        return response()->json($data);
    }

    public function edit($id)
    {
        $this->authorizeManage();

        $data = informasi_terkini::findOrFail($id);
        return response()->json($data);
    }

    public function delete($id)
    {
        $this->authorizeManage();

        $data = informasi_terkini::findOrFail($id);
        $data->delete();

        return response()->json($data);
    }

    public function aktif()
    {
        $data = $this->activeQuery()
            ->latest('updated_at')
            ->limit(self::MAX_ACTIVE_INFO)
            ->get();
        return response()->json($data);
    }
}
