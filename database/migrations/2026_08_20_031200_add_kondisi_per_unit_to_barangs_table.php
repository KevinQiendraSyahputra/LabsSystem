<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'kondisi_per_unit')) {
                $table->text('kondisi_per_unit')->nullable()->after('kondisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (Schema::hasColumn('barangs', 'kondisi_per_unit')) {
                $table->dropColumn('kondisi_per_unit');
            }
        });
    }
};
