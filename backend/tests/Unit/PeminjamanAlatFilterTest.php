<?php

namespace Tests\Unit;

use App\Http\Controllers\api\peminjamanController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PeminjamanAlatFilterTest extends TestCase
{
    private string $defaultConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConnection = config('database.default');

        config([
            'database.default' => 'peminjaman_alat_test',
            'database.connections.peminjaman_alat_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge('peminjaman_alat_test');

        Schema::connection('peminjaman_alat_test')->create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nabar')->nullable();
            $table->integer('jml')->default(0);
            $table->string('jenis_barang')->default('aset');
            $table->string('noreg')->nullable();
            $table->timestamps();
        });

        Schema::connection('peminjaman_alat_test')->create('katalogs', function (Blueprint $table) {
            $table->id();
            $table->text('topik');
            $table->timestamps();
        });

        Schema::connection('peminjaman_alat_test')->create('data_katalogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('katalog_id');
            $table->unsignedBigInteger('inventaris_id');
            $table->timestamps();
        });

        Schema::connection('peminjaman_alat_test')->create('pinjam_alats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('katalog_id');
            $table->date('tgl_pakai');
            $table->date('tgl_kembali')->nullable();
            $table->time('jam_pakai');
            $table->time('jam_kembali')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::connection('peminjaman_alat_test')->create('jumlah_pinjam_alats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_katalog_id');
            $table->unsignedBigInteger('pinjam_alat_id');
            $table->integer('minta')->nullable();
            $table->integer('diberi')->nullable();
            $table->integer('rusak')->nullable();
            $table->integer('hilang')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('peminjaman_alat_test');
        config(['database.default' => $this->defaultConnection]);

        parent::tearDown();
    }

    public function test_filter_topik_alat_keeps_diberi_separate_from_minta(): void
    {
        $inventarisId = DB::table('inventaris')->insertGetId([
            'nabar' => 'Lilin',
            'jml' => 40,
            'jenis_barang' => 'habis_pakai',
            'noreg' => 'LILIN-001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('katalogs')->insert([
            'id' => 10,
            'topik' => 'Praktikum lilin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dataKatalogId = DB::table('data_katalogs')->insertGetId([
            'katalog_id' => 10,
            'inventaris_id' => $inventarisId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pinjamAlatId = DB::table('pinjam_alats')->insertGetId([
            'katalog_id' => 10,
            'tgl_pakai' => '2026-05-14',
            'tgl_kembali' => '2026-05-14',
            'jam_pakai' => '08:00:00',
            'jam_kembali' => '10:00:00',
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('jumlah_pinjam_alats')->insert([
            'data_katalog_id' => $dataKatalogId,
            'pinjam_alat_id' => $pinjamAlatId,
            'minta' => 5,
            'diberi' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = (new peminjamanController())->filterTopikAlat(10, $pinjamAlatId);
        $rows = $response->getData();

        $this->assertCount(1, $rows);
        $this->assertSame(5, (int) $rows[0]->minta);
        $this->assertSame(0, (int) $rows[0]->diberi);
        $this->assertSame(0, (int) $rows[0]->diberi_asli);
        $this->assertSame(40, (int) $rows[0]->jml);
    }
}
