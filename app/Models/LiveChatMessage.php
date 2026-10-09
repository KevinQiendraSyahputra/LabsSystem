<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_chat_id',
        'sender',
        'sender_name',
        'message',
        'telegram_message_id',
        'discord_message_id',
        'is_read',
    ];

    public function liveChat()
    {
        return $this->belongsTo(LiveChat::class, 'live_chat_id');
    }
}
