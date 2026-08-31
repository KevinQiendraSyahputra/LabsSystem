<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('siswa')->after('password');
            $table->string('nomor_induk')->nullable()->after('role'); // NIS / NIP
            $table->string('kelas_atau_jabatan')->nullable()->after('nomor_induk');
            $table->string('telepon')->nullable()->after('kelas_atau_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nomor_induk', 'kelas_atau_jabatan', 'telepon']);
        });
    }
};