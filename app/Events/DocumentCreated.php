<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;

class DocumentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $document;
    public $documentId;
    public $documentName;
    public $additionalInfo;
    public $fileInput;
    public $isVerified;
    public $documentGeneratorId;

    public function __construct(Document $document, $documentId, $documentName, $additionalInfo = null, $fileInput = null, $isVerified = 0, $documentGeneratorId = null)
    {
        $this->document = $document;
        $this->documentId = $documentId;
        $this->documentName = $documentName;
        $this->additionalInfo = $additionalInfo;
        $this->fileInput = $fileInput;
        $this->isVerified = $isVerified;
        $this->documentGeneratorId = $documentGeneratorId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
