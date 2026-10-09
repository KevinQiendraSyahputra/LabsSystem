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
        Schema::table('live_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('live_chats', 'admin_typing_until')) {
                $table->timestamp('admin_typing_until')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_chats', function (Blueprint $table) {
            if (Schema::hasColumn('live_chats', 'admin_typing_until')) {
                $table->dropColumn('admin_typing_until');
            }
        });
    }
};
