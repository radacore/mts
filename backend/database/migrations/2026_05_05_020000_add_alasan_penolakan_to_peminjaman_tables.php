<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjam_labs', function (Blueprint $table) {
            $table->text('alasan_penolakan')->nullable()->after('status');
        });

        Schema::table('pinjam_alats', function (Blueprint $table) {
            $table->text('alasan_penolakan')->nullable()->after('status');
        });

        Schema::table('pinjam_lains', function (Blueprint $table) {
            $table->text('alasan_penolakan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pinjam_labs', function (Blueprint $table) {
            $table->dropColumn('alasan_penolakan');
        });

        Schema::table('pinjam_alats', function (Blueprint $table) {
            $table->dropColumn('alasan_penolakan');
        });

        Schema::table('pinjam_lains', function (Blueprint $table) {
            $table->dropColumn('alasan_penolakan');
        });
    }
};
