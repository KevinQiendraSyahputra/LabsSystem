<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'telegram_chat_id',
        'telegram_last_message_id',
        'status',
        'admin_typing_until',
    ];

    protected $casts = [
        'admin_typing_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(LiveChatMessage::class, 'live_chat_id')->orderBy('id', 'asc');
    }
}
