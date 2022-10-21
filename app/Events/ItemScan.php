<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemScan implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $goodsFlowId;

    public string $boxId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($goodsFlowId, $boxId)
    {
        $this->goodsFlowId = $goodsFlowId;
        $this->boxId = $boxId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('ItemScanChannel' . $this->boxId);
    }
}
