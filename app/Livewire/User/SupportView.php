<?php

namespace App\Livewire\User;

use Livewire\WithFileUploads;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\MediaService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
class SupportView extends Component
{
    use WithFileUploads;

    public $ticket;
    public $message = '';
    public $media;
    public $ticketId;
    public $mediaPreview;

    protected $rules = [
        'message' => 'nullable|string',
        'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
    ];

    protected $listeners = ['refreshMessages' => 'refreshTicket'];

    public function mount($ticketId, MediaService $mediaService)
    {
        $this->ticketId = $ticketId;
        $this->loadTicketData($mediaService);
    }

    /**
     * Load ticket data with proper media processing
     */
    private function loadTicketData(MediaService $mediaService)
    {
        $this->ticket = Ticket::with(['messages', 'user'])
            ->where('user_id', auth()->id())
            ->where('ticket_id', $this->ticketId)
            ->firstOrFail();

        // Process messages and attach media URLs
        $processedMessages = collect();
        $processedIds = [];

        foreach ($this->ticket->messages->sortBy('created_at') as $message) {
            // Skip if already processed (prevents duplicates)
            if (in_array($message->id, $processedIds)) {
                continue;
            }

            // Add to processed list
            $processedIds[] = $message->id;

            // Attach media URL if applicable
            if ($message->media_id) {
                $media = $mediaService->getMediaById($message->media_id);
                $message->media_url = $media ? $mediaService->getMediaUrl($media) : null;
            } else {
                $message->media_url = null;
            }

            $processedMessages->push($message);
        }

        // Replace messages relation with our processed collection
        $this->ticket->setRelation('messages', $processedMessages);
    }

    /**
     * Public method to refresh ticket data
     */
    public function refreshTicket(MediaService $mediaService)
    {
        $this->loadTicketData($mediaService);
    }

    /**
     * Handle file upload preview
     */
    public function updatedMedia()
    {
        if ($this->media) {
            try {
                $this->mediaPreview = $this->media->temporaryUrl();
            } catch (\Exception $e) {
                // If temporary URL can't be generated (for non-image files)
                $this->mediaPreview = null;
            }
        } else {
            $this->mediaPreview = null;
        }

        $this->validate([
            'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);
    }

    /**
     * Send reply to support ticket
     */
    public function sendReply(MediaService $mediaService)
    {
        $this->validate();
    
        if (empty($this->message) && !$this->media) {
            session()->flash('error', 'Please provide a message or attach a file.');
            return;
        }
    
        DB::beginTransaction();
    
        try {
            $mediaId = null;
    
            if ($this->media) {
                $media = $mediaService->uploadMedia($this->media, 'tickets');
                $mediaId = $media->id;
            }
    
            TicketMessage::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'sent_by' => 'user',
                'message' => $this->message,
                'media_id' => $mediaId,
                'seen_status' => false,
            ]);
    
            if ($this->ticket->status === 'closed') {
                $this->ticket->update(['status' => 'open']);
            }
    
            $this->ticket->touch();
    
            DB::commit();
    
            // ✅ Reset form fields
            $this->message = '';
            $this->media = null;
            $this->mediaPreview = null;
    
   
    
            // ✅ Refresh ticket data
            $this->loadTicketData($mediaService);
    
            session()->flash('success', 'Your reply has been sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to send message. Please try again.');
        }
    }
    
  
    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user.support-view');
    }
}

