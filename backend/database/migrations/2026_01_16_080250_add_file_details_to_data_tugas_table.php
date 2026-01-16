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
        Schema::table('data_tugas', function (Blueprint $table) {
            if (!Schema::hasColumn('data_tugas', 'file_name')) {
                $table->string('file_name')->nullable()->after('file');
            }
            if (!Schema::hasColumn('data_tugas', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('file_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_tugas', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_size']);
        });
    }
};
