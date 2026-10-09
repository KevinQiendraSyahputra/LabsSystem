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
            if (!Schema::hasColumn('live_chats', 'discord_channel_id')) {
                $table->string('discord_channel_id', 64)->nullable()->after('telegram_last_message_id');
            }
            if (!Schema::hasColumn('live_chats', 'discord_last_message_id')) {
                $table->string('discord_last_message_id', 64)->nullable()->after('discord_channel_id');
            }
        });

        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('live_chat_messages', 'discord_message_id')) {
                $table->string('discord_message_id', 64)->nullable()->after('telegram_message_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_chats', function (Blueprint $table) {
            $colsToDrop = array_filter(['discord_channel_id', 'discord_last_message_id'], fn($c) => Schema::hasColumn('live_chats', $c));
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });

        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('live_chat_messages', 'discord_message_id')) {
                $table->dropColumn('discord_message_id');
            }
        });
    }
};
