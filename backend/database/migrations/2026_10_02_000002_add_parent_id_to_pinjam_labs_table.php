<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tautan pengajuan susulan ("Tambah Kekurangan") ke pengajuan induk.
     * Nullable + nullOnDelete agar riwayat susulan tetap utuh
     * meski pengajuan induk dihapus.
     */
    public function up()
    {
        Schema::table('pinjam_labs', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')
                ->references('id')->on('pinjam_labs')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('pinjam_labs', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
