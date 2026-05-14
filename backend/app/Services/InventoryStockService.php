<?php

namespace App\Services;

use App\Models\inventaris;
use App\Models\inventaris_mutation;

class InventoryStockService
{
    private const OUTGOING_MUTATION_TYPES = ['keluar', 'pemutihan'];
    private const DAMAGE_MUTATION_TYPE = 'rusak';

    public function ensureConsumableInitialStock(inventaris $inventaris, ?int $tahun = null, ?int $createdBy = null): void
    {
        if (($inventaris->jenis_barang ?? 'aset') !== 'habis_pakai') {
            return;
        }

        $initial = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'initial')
            ->sum('qty');

        if ($initial > 0) {
            return;
        }

        $masuk = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'masuk')
            ->sum('qty');
        $keluar = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->whereIn('jenis', self::OUTGOING_MUTATION_TYPES)
            ->sum('qty');

        $baseline = max((int) $inventaris->jml - $masuk + $keluar, 0);
        if ($baseline <= 0) {
            return;
        }

        $initialMutation = inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'initial')
            ->orderBy('id')
            ->first();

        if ($initialMutation) {
            $initialMutation->update([
                'qty' => $baseline,
                'tahun' => $initialMutation->tahun ?: ($tahun ?? (int) date('Y')),
            ]);

            return;
        }

        inventaris_mutation::create([
            'inventaris_id' => $inventaris->id,
            'tahun' => $tahun ?? (int) date('Y'),
            'qty' => $baseline,
            'jenis' => 'initial',
            'keterangan' => 'Data awal inventaris',
            'created_by' => $createdBy,
        ]);
    }

    public function sync(inventaris $inventaris): inventaris
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
            $rusak = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
                ->where('jenis', self::DAMAGE_MUTATION_TYPE)
                ->sum('qty');
            $pemutihanRusak = (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
                ->where('jenis', 'pemutihan')
                ->where('kondisi_asal', 'rusak')
                ->sum('qty');

            $inventaris->konrusak = max($rusak - $pemutihanRusak, 0);
            $inventaris->konbaik = max($stok - (int) $inventaris->konrusak, 0);
        }

        $inventaris->save();

        return $inventaris->fresh();
    }
}
