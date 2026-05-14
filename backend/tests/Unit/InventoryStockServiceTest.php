<?php

namespace Tests\Unit;

use App\Models\inventaris;
use App\Models\inventaris_mutation;
use App\Services\InventoryStockService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventoryStockServiceTest extends TestCase
{
    private string $defaultConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConnection = config('database.default');

        config([
            'database.default' => 'inventory_test',
            'database.connections.inventory_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge('inventory_test');
        Schema::connection('inventory_test')->create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('noreg')->nullable();
            $table->string('katalog')->nullable();
            $table->string('nabar')->nullable();
            $table->text('spec')->nullable();
            $table->string('satuan')->nullable();
            $table->integer('vol')->default(0);
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->string('produsen')->nullable();
            $table->string('asal')->nullable();
            $table->string('thn_masuk')->nullable();
            $table->string('thn_pakai')->nullable();
            $table->integer('jml')->default(0);
            $table->string('kondisi')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->text('ket')->nullable();
            $table->integer('konbaik')->default(0);
            $table->integer('konrusak')->default(0);
            $table->string('jenis_barang')->default('aset');
            $table->integer('stok_minimum')->nullable();
            $table->timestamps();
        });

        Schema::connection('inventory_test')->create('inventaris_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventaris_id');
            $table->integer('tahun');
            $table->integer('qty');
            $table->string('jenis', 50);
            $table->string('keterangan')->nullable();
            $table->string('kondisi_asal', 20)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('inventory_test');
        config(['database.default' => $this->defaultConnection]);

        parent::tearDown();
    }

    public function test_sync_updates_consumable_stock_from_mutations(): void
    {
        $inventaris = inventaris::create([
            'noreg' => 'HP-001',
            'katalog' => 'Bahan Praktikum',
            'nabar' => 'Kertas Lakmus',
            'spec' => 'Consumable test item',
            'satuan' => 'pcs',
            'vol' => 10,
            'thn_masuk' => '2026',
            'thn_pakai' => '2026',
            'jml' => 10,
            'kondisi' => 'baik',
            'lokasi' => 'Lab',
            'konbaik' => 10,
            'konrusak' => 0,
            'jenis_barang' => 'habis_pakai',
        ]);

        inventaris_mutation::create([
            'inventaris_id' => $inventaris->id,
            'tahun' => 2026,
            'qty' => 10,
            'jenis' => 'initial',
        ]);
        inventaris_mutation::create([
            'inventaris_id' => $inventaris->id,
            'tahun' => 2026,
            'qty' => 4,
            'jenis' => 'keluar',
            'keterangan' => 'Peminjaman alat #1 disetujui (consumable)',
        ]);

        $synced = app(InventoryStockService::class)->sync($inventaris);

        $this->assertSame(6, (int) $synced->jml);
        $this->assertSame(6, (int) $synced->konbaik);
        $this->assertSame(0, (int) $synced->konrusak);
    }

    public function test_consumable_initial_stock_is_backfilled_before_first_outgoing_mutation(): void
    {
        $inventaris = inventaris::create([
            'noreg' => 'HP-002',
            'katalog' => 'Bahan Praktikum',
            'nabar' => 'Lilin',
            'spec' => 'Consumable without initial mutation',
            'satuan' => 'pcs',
            'vol' => 5,
            'thn_masuk' => '2026',
            'thn_pakai' => '2026',
            'jml' => 5,
            'kondisi' => 'baik',
            'lokasi' => 'Lab',
            'konbaik' => 5,
            'konrusak' => 0,
            'jenis_barang' => 'habis_pakai',
        ]);

        $service = app(InventoryStockService::class);
        $service->ensureConsumableInitialStock($inventaris, 2026);

        inventaris_mutation::create([
            'inventaris_id' => $inventaris->id,
            'tahun' => 2026,
            'qty' => 3,
            'jenis' => 'keluar',
            'keterangan' => 'Peminjaman alat #304 disetujui (consumable)',
        ]);

        $synced = $service->sync($inventaris->fresh());

        $this->assertSame(5, (int) inventaris_mutation::where('inventaris_id', $inventaris->id)
            ->where('jenis', 'initial')
            ->sum('qty'));
        $this->assertSame(2, (int) $synced->jml);
        $this->assertSame(2, (int) $synced->konbaik);
        $this->assertSame(0, (int) $synced->konrusak);
    }
}
