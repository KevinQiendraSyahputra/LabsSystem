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
            if (!Schema::hasColumn('live_chats', 'admin_name')) {
                $table->string('admin_name', 100)->nullable()->after('user_role');
            }
        });

        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('live_chat_messages', 'sender_name')) {
                $table->string('sender_name', 100)->nullable()->after('sender');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_chats', function (Blueprint $table) {
            if (Schema::hasColumn('live_chats', 'admin_name')) {
                $table->dropColumn('admin_name');
            }
        });

        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('live_chat_messages', 'sender_name')) {
                $table->dropColumn('sender_name');
            }
        });
    }
};
