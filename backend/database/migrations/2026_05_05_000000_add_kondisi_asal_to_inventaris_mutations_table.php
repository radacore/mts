<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_mutations', function (Blueprint $table) {
            if (!Schema::hasColumn('inventaris_mutations', 'kondisi_asal')) {
                $table->string('kondisi_asal', 20)->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_mutations', function (Blueprint $table) {
            if (Schema::hasColumn('inventaris_mutations', 'kondisi_asal')) {
                $table->dropColumn('kondisi_asal');
            }
        });
    }
};
