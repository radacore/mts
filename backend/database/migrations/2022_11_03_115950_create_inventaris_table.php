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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('noreg');
            $table->string('katalog');
            $table->string('nabar');
            $table->text('spec');
            $table->string('satuan');
            $table->integer('vol');
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->string('produsen')->nullable();
            $table->string('asal')->nullable();
            $table->string('thn_masuk');
            $table->string('thn_pakai');
            $table->integer('jml');
            $table->string('kondisi');
            $table->string('lokasi');
            $table->string('foto')->nullable();
            $table->text('ket')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventaris');
    }
};
