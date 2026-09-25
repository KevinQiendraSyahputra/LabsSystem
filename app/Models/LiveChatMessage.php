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
        'message',
        'telegram_message_id',
        'is_read',
    ];

    public function liveChat()
    {
        return $this->belongsTo(LiveChat::class, 'live_chat_id');
    }
}
