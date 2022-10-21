<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewScanFileUpload implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $states = [
        'fileSelected' => true,
        'fileUploaded' => false,
        'fileRed' => false,
        'fileMakingChunks' => false,
        'fileUploadingChunks' => false,
        'scanCreated' => false,
        'chunksCreated' => 0,
        'chunksUploaded' => 0,
    ];

    private int $userId;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $key, User $user)
    {
        $this->userId = $user->id;
        $this->states[$key] = true;
    }

    public function next(string $key): static
    {
        $this->states[$key] = true;
        return $this;
    }

    public function setChunksCreated(int $chunks): static
    {
        $this->states['chunksCreated'] = $chunks;
        return $this;
    }

    public function setChunksUploaded(int $chunks): static
    {
        $this->states['chunksUploaded'] = $chunks;
        return $this;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('NewScanFileUploadChannel' . $this->userId);
    }
}
