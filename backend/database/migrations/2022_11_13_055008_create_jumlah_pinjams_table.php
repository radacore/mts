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
        Schema::create('jumlah_pinjams', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('data_katalog_id')->unsigned();
            $table->bigInteger('pinjam_lab_id')->unsigned();
            $table->integer('minta')->nullable();
            $table->integer('diberi')->nullable();
            $table->timestamps();
            $table->foreign('data_katalog_id')->references('id')->on('data_katalogs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pinjam_lab_id')->references('id')->on('pinjam_labs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jumlah_pinjams');
    }
};
