<?php

use App\Models\inventaris;
use App\Models\inventaris_mutation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $mutations = inventaris_mutation::where('jenis', 'keluar')
                ->where('keterangan', 'like', 'Peminjaman alat #% dikembalikan - rusak:%')
                ->get();

            foreach ($mutations as $mutation) {
                $rusak = null;
                $hilang = null;

                if (preg_match('/rusak:\s*(\d+)/i', (string) $mutation->keterangan, $matches)) {
                    $rusak = (int) $matches[1];
                }
                if (preg_match('/hilang:\s*(\d+)/i', (string) $mutation->keterangan, $matches)) {
                    $hilang = (int) $matches[1];
                }

                if ($rusak === null || $rusak <= 0 || ($hilang ?? 0) > 0) {
                    continue;
                }

                $mutation->update([
                    'jenis' => 'rusak',
                    'qty' => $rusak,
                    'kondisi_asal' => 'baik',
                    'keterangan' => preg_replace('/\s*,?\s*hilang:\s*0\s*$/i', '', (string) $mutation->keterangan),
                ]);
            }

            $inventarisIds = $mutations->pluck('inventaris_id')->unique()->values();
            foreach ($inventarisIds as $inventarisId) {
                $this->syncInventaris((int) $inventarisId);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $mutations = inventaris_mutation::where('jenis', 'rusak')
                ->where('keterangan', 'like', 'Peminjaman alat #% dikembalikan - rusak:%')
                ->get();

            foreach ($mutations as $mutation) {
                $mutation->update([
                    'jenis' => 'keluar',
                    'kondisi_asal' => null,
                    'keterangan' => (string) $mutation->keterangan . ', hilang: 0',
                ]);
            }

            $inventarisIds = $mutations->pluck('inventaris_id')->unique()->values();
            foreach ($inventarisIds as $inventarisId) {
                $this->syncInventaris((int) $inventarisId);
            }
        });
    }

    private function syncInventaris(int $inventarisId): void
    {
        $inventaris = inventaris::find($inventarisId);
        if (!$inventaris) {
            return;
        }

        $initial = (int) inventaris_mutation::where('inventaris_id', $inventarisId)
            ->where('jenis', 'initial')
            ->sum('qty');
        $masuk = (int) inventaris_mutation::where('inventaris_id', $inventarisId)
            ->where('jenis', 'masuk')
            ->sum('qty');
        $keluar = (int) inventaris_mutation::where('inventaris_id', $inventarisId)
            ->whereIn('jenis', ['keluar', 'pemutihan'])
            ->sum('qty');
        $rusak = (int) inventaris_mutation::where('inventaris_id', $inventarisId)
            ->where('jenis', 'rusak')
            ->sum('qty');
        $pemutihanRusak = (int) inventaris_mutation::where('inventaris_id', $inventarisId)
            ->where('jenis', 'pemutihan')
            ->where('kondisi_asal', 'rusak')
            ->sum('qty');

        $stok = max($initial + $masuk - $keluar, 0);
        $konrusak = max($rusak - $pemutihanRusak, 0);

        $inventaris->jml = $stok;
        if (($inventaris->jenis_barang ?? 'aset') === 'habis_pakai') {
            $inventaris->konrusak = 0;
            $inventaris->konbaik = $stok;
        } else {
            $inventaris->konrusak = $konrusak;
            $inventaris->konbaik = max($stok - $konrusak, 0);
        }
        $inventaris->save();
    }
};
