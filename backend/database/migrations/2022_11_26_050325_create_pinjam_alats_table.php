<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinjam_alats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('katalog_id')->unsigned();
            $table->bigInteger('kelas_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->date('tgl_pakai');
            $table->date('tgl_kembali')->nullable();
            $table->time('jam_pakai');
            $table->time('jam_kembali')->nullable();
            $table->integer('jam')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('katalog_id')->references('id')->on('katalogs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinjam_alats');
    }
};
