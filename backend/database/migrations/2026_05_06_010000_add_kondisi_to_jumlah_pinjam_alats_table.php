<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tambah kolom rusak & hilang untuk mencatat kondisi pengembalian per item.
     * - rusak: jumlah unit yang dikembalikan dalam kondisi rusak
     * - hilang: jumlah unit yang tidak dikembalikan
     * - utuh (computed) = diberi - rusak - hilang
     */
    public function up(): void
    {
        Schema::table('jumlah_pinjam_alats', function (Blueprint $table) {
            $table->integer('rusak')->default(0)->after('diberi');
            $table->integer('hilang')->default(0)->after('rusak');
        });
    }

    public function down(): void
    {
        Schema::table('jumlah_pinjam_alats', function (Blueprint $table) {
            $table->dropColumn(['rusak', 'hilang']);
        });
    }
};
