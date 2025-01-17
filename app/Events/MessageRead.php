<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */


    public $conversationId;
    public $receiverId;

    public function __construct($conversationId , $receiverId)
    {
            $this->conversationId = $conversationId;
            $this->receiverId = $receiverId;
    }

    public function broadcastWith()
    {
        return [
            'type' => 'MessageRead',
            'conversationId' => $this->conversationId,
            'receiver_id' => $this->receiverId,
        ];

    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.'. $this->receiverId);   // مقروءه  بالنسبه للراسل

    }
}
