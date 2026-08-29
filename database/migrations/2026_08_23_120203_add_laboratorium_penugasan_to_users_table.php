<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk menentukan lab penugasan koordinator_lab / kepala_lab
            $table->string('laboratorium_penugasan')->nullable()->after('kelas_atau_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('laboratorium_penugasan');
        });
    }
};
