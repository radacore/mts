<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modul_lkpd', function (Blueprint $table) {
            $table->id();
            $table->string('judul');                     // Judul modul
            $table->string('file_path');                 // Path di storage: "modul/nama_file.pdf"
            $table->string('file_name');                 // Nama asli: "LKPD_Kelas_X.pdf"
            $table->string('mime_type')->nullable();     // application/pdf, dll
            $table->unsignedBigInteger('uploaded_by');   // ID user (laboran)
            $table->timestamps();

            // Relasi ke users
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('modul_lkpd');
    }
};