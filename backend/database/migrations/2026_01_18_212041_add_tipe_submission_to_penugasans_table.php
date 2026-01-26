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
        Schema::table('penugasans', function (Blueprint $table) {
            $table->boolean('tipe_esay')->default(true)->after('soal');
            $table->boolean('tipe_upload')->default(true)->after('tipe_esay');
            $table->boolean('tipe_link')->default(true)->after('tipe_upload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropColumn(['tipe_esay', 'tipe_upload', 'tipe_link']);
        });
    }
};
