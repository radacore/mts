<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            // Tambah kolom modul_id (foreign key ke modul_lkpd)
            $table->unsignedBigInteger('modul_id')->nullable()->after('file');
            $table->foreign('modul_id')
                  ->references('id')
                  ->on('modul_lkpd')
                  ->onDelete('set null');

            // Tambah kolom link_tambahan
            $table->string('link_tambahan')->nullable()->after('modul_id');
        });
    }

    public function down()
    {
        Schema::table('materi_ajars', function (Blueprint $table) {
            // Hapus foreign key dulu, baru kolom
            $table->dropForeign(['modul_id']);
            $table->dropColumn(['modul_id', 'link_tambahan']);
        });
    }
};