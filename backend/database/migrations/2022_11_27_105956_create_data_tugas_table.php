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
        Schema::create('data_tugas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('penugasan_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('esay')->nullable();
            $table->text('file')->nullable();
            $table->char('nilai',5)->nullable();
            $table->timestamps();
            $table->foreign('penugasan_id')->references('id')->on('penugasans')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('data_tugas');
    }
};
