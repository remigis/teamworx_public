<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRequiredListCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array|mixed
     */
    public mixed $needToOverwrite;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($needToOverwrite = [])
    {
        $this->needToOverwrite = $needToOverwrite;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('NewRequiredListCreatedEventChannel');
    }
}
