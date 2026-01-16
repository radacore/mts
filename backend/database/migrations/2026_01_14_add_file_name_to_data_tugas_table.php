<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_tugas', function (Blueprint $table) {
            // ✅ Tambah field untuk menyimpan nama file asli
            $table->string('file_name')->nullable()->after('file');
            // ✅ Tambah field untuk menyimpan ukuran file (dalam bytes)
            $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_tugas', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_size']);
        });
    }
};
