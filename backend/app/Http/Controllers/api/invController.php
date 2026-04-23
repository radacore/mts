<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\inventaris;
use App\Models\inventaris_mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class invController extends Controller
{
    private function extractYearFromString(?string $value): int
    {
        if ($value && preg_match('/\b(19|20)\d{2}\b/', $value, $matches)) {
            return (int) $matches[0];
        }

        return 0;
    }

    private function resolveInitialYear(inventaris $inventaris): int
    {
        $yearFromField = $this->extractYearFromString($inventaris->thn_masuk);
        if ($yearFromField > 0) {
            return $yearFromField;
        }

        if (!empty($inventaris->created_at)) {
            return (int) date('Y', strtotime((string) $inventaris->created_at));
        }

        return (int) date('Y');
    }

    private function authorizeInventarisAccess(): void
    {
        $user = auth()->user();

        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            abort(403, 'Anda tidak berhak mengakses inventaris.');
        }
    }

    private function syncStockFromMutations(inventaris $inventaris): inventaris
    {
        $initial = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'initial')
            ->sum('qty');

        $masuk = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'masuk')
            ->sum('qty');

        $keluar = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'keluar')
            ->sum('qty');

        $stok = max($initial + $masuk - $keluar, 0);
        $inventaris->jml = $stok;

        if (($inventaris->jenis_barang ?? 'aset') === 'habis_pakai') {
            $inventaris->konrusak = 0;
            $inventaris->konbaik = $stok;
        } else {
            $inventaris->konbaik = max($stok - (int) $inventaris->konrusak, 0);
        }

        $inventaris->save();

        return $inventaris->fresh();
    }

    private function availableStockExcludingMutation(int $inventarisId, ?int $excludeMutationId = null): int
    {
        $baseQuery = inventaris_mutation::where('inventaris_id', $inventarisId);

        if ($excludeMutationId) {
            $baseQuery->where('id', '!=', $excludeMutationId);
        }

        $initial = (clone $baseQuery)->where('jenis', 'initial')->sum('qty');
        $masuk = (clone $baseQuery)->where('jenis', 'masuk')->sum('qty');
        $keluar = (clone $baseQuery)->where('jenis', 'keluar')->sum('qty');

        return max((int) $initial + (int) $masuk - (int) $keluar, 0);
    }

    public function index(Request $request)
    {
        $this->authorizeInventarisAccess();

        $query = inventaris::query();

        $tahun = $request->query('tahun');
        if (!empty($tahun)) {
            $query->where(function ($q) use ($tahun) {
                $q->where('thn_masuk', 'like', '%' . $tahun . '%')
                  ->orWhereHas('mutations', function ($mq) use ($tahun) {
                      $mq->where('tahun', (int) $tahun);
                  });
            });
        }

        if ((int) $request->query('rusak_only', 0) === 1) {
            $query->where('konrusak', '>', 0);
        }

        $stokStatus = $request->query('stok_status');
        if ($stokStatus === 'habis') {
            $query->where('jml', '<=', 0);
        } elseif ($stokStatus === 'menipis') {
            $defaultStokMinimum = 5;
            $query->where('jml', '>', 0)
                ->whereRaw('jml <= COALESCE(stok_minimum, ?)', [$defaultStokMinimum]);
        }

        $data = $query->latest()->get();
        return response()->json($data);
    }
    public function inventarisPost(Request $request)
    {
        $this->authorizeInventarisAccess();

        $request->validate([
            'noreg'=>'required',
            'katalog'=>'required',
            'nabar'=>'required',
            'satuan'=>'required',
            'vol'=>'required|numeric',
            'thn_masuk'=>'required',
            'thn_pakai'=>'required',
            'jml'=>'required|numeric',
            'lokasi'=>'required',
            'spec'=>'required',
            'konbaik'=>'required|numeric',
            'konrusak'=>'required|numeric',
            'jenis_barang' => 'nullable|in:aset,habis_pakai',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        $isCreate = empty($request->id);

        $data=inventaris::updateOrCreate(['id'=>$request->id],[
            'noreg'=>$request->noreg,
            'katalog'=>$request->katalog,
            'nabar'=>$request->nabar,
            'satuan'=>$request->satuan,
            'vol'=>$request->vol,
            'merek'=>$request->merek,
            'tipe'=>$request->tipe,
            'produsen'=>$request->produsen,
            'asal'=>$request->asal,
            'thn_masuk'=>$request->thn_masuk,
            'thn_pakai'=>$request->thn_pakai,
            'jml'=>$request->jml,
            'kondisi'=>$request->jml,
            'konbaik'=>$request->konbaik,
            'konrusak'=>$request->konrusak,
            'lokasi'=>$request->lokasi,
            'spec'=>$request->spec,
            'jenis_barang' => $request->jenis_barang ?? 'aset',
            'stok_minimum' => $request->stok_minimum,
        ]);

        if ($isCreate) {
            $initialYear = $this->extractYearFromString($request->thn_masuk);
            if ($initialYear <= 0) {
                $initialYear = (int) date('Y');
            }

            inventaris_mutation::create([
                'inventaris_id' => $data->id,
                'tahun' => $initialYear,
                'qty' => (int) $request->jml,
                'jenis' => 'initial',
                'keterangan' => 'Stok awal saat barang dibuat',
                'created_by' => auth()->id(),
            ]);
        }

        if (($data->jenis_barang ?? 'aset') === 'habis_pakai') {
            $data->konrusak = 0;
            $data->konbaik = (int) $data->jml;
            $data->save();
        }

        return response()->json($data);
    }
    public function inventarisEdit($id)
    {
        $this->authorizeInventarisAccess();

        $data=inventaris::where('id', $id)->first();
        return response()->json($data);
    }
    public function inventarisHapus($id)
    {
        $this->authorizeInventarisAccess();

        if($id){
            $hapus=inventaris::find($id);
            $hapus->delete();
        }
    }
    public function inventarisFoto(Request $request)
    {
        $this->authorizeInventarisAccess();

        $request->validate([
            'foto'=>'required|image|mimes:jpeg,png,jpg,gif,svg,'
        ]);
        if($request->id){
            $upload=inventaris::find($request->id);
            $foto=$request->file('foto')->store('inventaris','public');
            $upload->update([
                'foto'=>$foto
            ]);
        }
        return response()->json($upload);
    }

    public function riwayat($id)
    {
        $this->authorizeInventarisAccess();

        $inventaris = inventaris::findOrFail($id);

        $initial = inventaris_mutation::where('inventaris_id', $id)
            ->where('jenis', 'initial')
            ->first();

        if (!$initial) {
            inventaris_mutation::create([
                'inventaris_id' => $inventaris->id,
                'tahun' => $this->resolveInitialYear($inventaris),
                'qty' => (int) $inventaris->jml,
                'jenis' => 'initial',
                'keterangan' => 'Data awal inventaris',
                'created_by' => auth()->id(),
            ]);
        } else {
            $resolvedYear = $this->resolveInitialYear($inventaris);
            $autoNotes = ['Data awal inventaris', 'Stok awal saat barang dibuat', 'Stok awal (backfill)'];
            if (in_array((string) $initial->keterangan, $autoNotes, true) && (int) $initial->tahun !== $resolvedYear) {
                $initial->tahun = $resolvedYear;
                $initial->save();
            }
        }

        $items = inventaris_mutation::where('inventaris_id', $id)
            ->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $summary = inventaris_mutation::where('inventaris_id', $id)
            ->whereIn('jenis', ['initial', 'masuk'])
            ->selectRaw('tahun, COUNT(*) as frekuensi, SUM(qty) as total_qty')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->get();

        return response()->json([
            'inventaris' => $inventaris,
            'summary_per_tahun' => $summary,
            'items' => $items,
        ]);
    }

    public function tambahStok(Request $request, $id)
    {
        $this->authorizeInventarisAccess();

        $request->validate([
            'tahun' => 'required|digits:4',
            'qty' => 'required|integer|min:1',
            'jenis' => 'nullable|in:masuk,keluar',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $inventaris = inventaris::findOrFail($id);
        $jenis = $request->jenis ?? 'masuk';

        if ($jenis === 'keluar' && (int) $request->qty > (int) $inventaris->jml) {
            return response()->json([
                'message' => 'Qty pemakaian melebihi stok tersedia',
            ], 422);
        }

        inventaris_mutation::create([
            'inventaris_id' => $inventaris->id,
            'tahun' => (int) $request->tahun,
            'qty' => (int) $request->qty,
            'jenis' => $jenis,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $inventaris = $this->syncStockFromMutations($inventaris);

        $message = $jenis === 'keluar'
            ? 'Riwayat pemakaian berhasil disimpan'
            : 'Penambahan stok berhasil disimpan';

        return response()->json([
            'success' => true,
            'message' => $message,
            'inventaris' => $inventaris,
        ]);
    }

    public function updateRiwayat(Request $request, $id, $riwayatId)
    {
        $this->authorizeInventarisAccess();

        $request->validate([
            'tahun' => 'required|digits:4',
            'qty' => 'required|integer|min:1',
            'jenis' => 'required|in:initial,masuk,keluar',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $inventaris = inventaris::findOrFail($id);
        $riwayat = inventaris_mutation::where('inventaris_id', $id)->where('id', $riwayatId)->firstOrFail();

        if ($riwayat->jenis === 'initial' && $request->jenis !== 'initial') {
            return response()->json([
                'message' => 'Jenis data awal tidak bisa diubah',
            ], 422);
        }

        if ($riwayat->jenis !== 'initial' && $request->jenis === 'initial') {
            return response()->json([
                'message' => 'Hanya satu data awal yang diperbolehkan',
            ], 422);
        }

        if ($request->jenis === 'keluar') {
            $stokTersedia = $this->availableStockExcludingMutation((int) $id, (int) $riwayatId);
            if ((int) $request->qty > $stokTersedia) {
                return response()->json([
                    'message' => 'Qty pemakaian melebihi stok tersedia',
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $riwayat, $inventaris) {
            $riwayat->update([
                'tahun' => (int) $request->tahun,
                'qty' => (int) $request->qty,
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
            ]);

            $this->syncStockFromMutations($inventaris);
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil diperbarui',
        ]);
    }

    public function hapusRiwayat($id, $riwayatId)
    {
        $this->authorizeInventarisAccess();

        $inventaris = inventaris::findOrFail($id);
        $riwayat = inventaris_mutation::where('inventaris_id', $id)->where('id', $riwayatId)->firstOrFail();

        if ($riwayat->jenis === 'initial') {
            return response()->json([
                'message' => 'Data awal tidak boleh dihapus',
            ], 422);
        }

        DB::transaction(function () use ($riwayat, $inventaris) {
            $riwayat->delete();
            $this->syncStockFromMutations($inventaris);
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil dihapus',
        ]);
    }
}
