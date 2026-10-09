<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas_atau_jabatan', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No rollback needed as VARCHAR(255) is backwards-compatible
    }
};
