<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PeminjamanUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('peminjaman-tracker'),
        ];

        if (!empty($this->payload['user_id'])) {
            $channels[] = new Channel('user.' . $this->payload['user_id']);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'PeminjamanUpdated';
    }
}
