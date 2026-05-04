<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\inventaris;
use App\Models\inventaris_mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class invController extends Controller
{
    private const OUTGOING_MUTATION_TYPES = ['keluar', 'pemutihan'];
    private const WHITENING_CONDITIONS = ['baik', 'rusak'];

    private function outgoingTypeLabel(string $jenis): string
    {
        return $jenis === 'pemutihan' ? 'pemutihan' : 'pemakaian';
    }

    private function resolveWhiteningCondition(?string $value): string
    {
        return in_array($value, self::WHITENING_CONDITIONS, true) ? $value : 'rusak';
    }

    private function conditionLabel(string $condition): string
    {
        return $condition === 'baik' ? 'baik' : 'rusak';
    }

    private function availableConditionStock(inventaris $inventaris, string $condition): int
    {
        return $condition === 'baik'
            ? (int) $inventaris->konbaik
            : (int) $inventaris->konrusak;
    }

    private function availableConditionStockForUpdate(inventaris $inventaris, inventaris_mutation $riwayat, string $condition): int
    {
        $available = $this->availableConditionStock($inventaris, $condition);

        if ($riwayat->jenis === 'pemutihan' && $this->resolveWhiteningCondition($riwayat->kondisi_asal ?? null) === $condition) {
            $available += (int) $riwayat->qty;
        }

        if ($riwayat->jenis === 'keluar' && $condition === 'baik') {
            $available += (int) $riwayat->qty;
        }

        return $available;
    }

    private function applyConditionDelta(inventaris $inventaris, string $condition, int $delta): void
    {
        if ($condition === 'baik') {
            $inventaris->konbaik = max((int) $inventaris->konbaik + $delta, 0);
        } else {
            $inventaris->konrusak = max((int) $inventaris->konrusak + $delta, 0);
        }

        $inventaris->save();
    }

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
            ->whereIn('jenis', self::OUTGOING_MUTATION_TYPES)
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
        $keluar = (clone $baseQuery)->whereIn('jenis', self::OUTGOING_MUTATION_TYPES)->sum('qty');

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

        if ((int) $request->query('pemutihan_only', 0) === 1) {
            $query->whereHas('mutations', function ($mq) {
                $mq->where('jenis', 'pemutihan');
            });
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
            'jenis' => 'nullable|in:masuk,keluar,pemutihan',
            'keterangan' => 'nullable|string|max:255',
            'kondisi_asal' => 'nullable|in:baik,rusak',
        ]);

        $inventaris = inventaris::findOrFail($id);
        $jenis = $request->jenis ?? 'masuk';
        $kondisiAsal = $jenis === 'pemutihan'
            ? $this->resolveWhiteningCondition($request->kondisi_asal)
            : null;

        if ($jenis === 'pemutihan' && blank($request->keterangan)) {
            return response()->json([
                'message' => 'Keterangan wajib diisi untuk pemutihan',
            ], 422);
        }

        if ($jenis === 'pemutihan' && (int) $request->qty > $this->availableConditionStock($inventaris, $kondisiAsal)) {
            return response()->json([
                'message' => 'Qty pemutihan melebihi stok ' . $this->conditionLabel($kondisiAsal) . ' tersedia',
            ], 422);
        }

        if ($jenis === 'keluar' && (int) $request->qty > (int) $inventaris->jml) {
            return response()->json([
                'message' => 'Qty ' . $this->outgoingTypeLabel($jenis) . ' melebihi stok tersedia',
            ], 422);
        }

        DB::transaction(function () use ($request, $inventaris, $jenis, $kondisiAsal) {
            inventaris_mutation::create([
                'inventaris_id' => $inventaris->id,
                'tahun' => (int) $request->tahun,
                'qty' => (int) $request->qty,
                'jenis' => $jenis,
                'kondisi_asal' => $kondisiAsal,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            if ($jenis === 'pemutihan') {
                $this->applyConditionDelta($inventaris, $kondisiAsal, -((int) $request->qty));
            }

            $this->syncStockFromMutations($inventaris);
        });

        $inventaris = $inventaris->fresh();

        if ($jenis === 'keluar') {
            $message = 'Riwayat pemakaian berhasil disimpan';
        } elseif ($jenis === 'pemutihan') {
            $message = 'Riwayat pemutihan berhasil disimpan';
        } else {
            $message = 'Penambahan stok berhasil disimpan';
        }

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
            'jenis' => 'required|in:initial,masuk,keluar,pemutihan',
            'keterangan' => 'nullable|string|max:255',
            'kondisi_asal' => 'nullable|in:baik,rusak',
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

        if ($request->jenis === 'pemutihan' && blank($request->keterangan)) {
            return response()->json([
                'message' => 'Keterangan wajib diisi untuk pemutihan',
            ], 422);
        }

        $kondisiAsal = $request->jenis === 'pemutihan'
            ? $this->resolveWhiteningCondition($request->kondisi_asal)
            : null;

        if ($request->jenis === 'pemutihan') {
            $stokKondisi = $this->availableConditionStockForUpdate($inventaris, $riwayat, $kondisiAsal);
            if ((int) $request->qty > $stokKondisi) {
                return response()->json([
                    'message' => 'Qty pemutihan melebihi stok ' . $this->conditionLabel($kondisiAsal) . ' tersedia',
                ], 422);
            }
        }

        if ($request->jenis === 'keluar') {
            $stokTersedia = $this->availableStockExcludingMutation((int) $id, (int) $riwayatId);
            if ((int) $request->qty > $stokTersedia) {
                return response()->json([
                    'message' => 'Qty ' . $this->outgoingTypeLabel($request->jenis) . ' melebihi stok tersedia',
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $riwayat, $inventaris, $kondisiAsal) {
            if ($riwayat->jenis === 'pemutihan') {
                $this->applyConditionDelta(
                    $inventaris,
                    $this->resolveWhiteningCondition($riwayat->kondisi_asal ?? null),
                    (int) $riwayat->qty
                );
            }

            $riwayat->update([
                'tahun' => (int) $request->tahun,
                'qty' => (int) $request->qty,
                'jenis' => $request->jenis,
                'kondisi_asal' => $kondisiAsal,
                'keterangan' => $request->keterangan,
            ]);

            if ($request->jenis === 'pemutihan') {
                $this->applyConditionDelta($inventaris, $kondisiAsal, -((int) $request->qty));
            }

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
            if ($riwayat->jenis === 'pemutihan') {
                $this->applyConditionDelta(
                    $inventaris,
                    $this->resolveWhiteningCondition($riwayat->kondisi_asal ?? null),
                    (int) $riwayat->qty
                );
            }

            $riwayat->delete();
            $this->syncStockFromMutations($inventaris);
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil dihapus',
        ]);
    }
}
