<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pinjam_lab_modul_lkpd', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjam_lab_id');
            $table->unsignedBigInteger('modul_lkpd_id');
            $table->timestamps();

            $table->foreign('pinjam_lab_id')->references('id')->on('pinjam_labs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('modul_lkpd_id')->references('id')->on('modul_lkpd')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['pinjam_lab_id', 'modul_lkpd_id']);
        });

        Schema::create('pinjam_alat_modul_lkpd', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjam_alat_id');
            $table->unsignedBigInteger('modul_lkpd_id');
            $table->timestamps();

            $table->foreign('pinjam_alat_id')->references('id')->on('pinjam_alats')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('modul_lkpd_id')->references('id')->on('modul_lkpd')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['pinjam_alat_id', 'modul_lkpd_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pinjam_alat_modul_lkpd');
        Schema::dropIfExists('pinjam_lab_modul_lkpd');
    }
};
