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
        Schema::create('data_katalogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('katalog_id')->unsigned();
            $table->bigInteger('inventaris_id')->unsigned();
            $table->timestamps();
            $table->foreign('katalog_id')->references('id')->on('katalogs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('inventaris_id')->references('id')->on('inventaris')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_katalogs');
    }
};
