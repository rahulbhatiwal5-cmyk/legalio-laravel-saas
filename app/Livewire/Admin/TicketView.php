<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\MediaService;

class TicketView extends Component
{
    use WithFileUploads;

    public $ticket;
    public $message = '';
    public $media;
    public $mediaName = null;
    
    protected $rules = [
        'message' => 'nullable|string',
        'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
    ];

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        // Load relations if they haven't been loaded yet
        if (!$this->ticket->relationLoaded('messages')) {
            $this->ticket->load(['messages.media', 'user']);
        }
    }

    public function updatedMedia()
    {
        if ($this->media) {
            $this->mediaName = $this->media->getClientOriginalName();
        }
    }

    public function adminReply()
    {
        $this->validate();
        
        $mediaId = null;

        if ($this->media) {
            $mediaService = app(MediaService::class);
            $uploadedMedia = $mediaService->uploadMedia($this->media, 'tickets');
            $mediaId = $uploadedMedia->id;
        }

        $message = new TicketMessage();
        $message->ticket_id = $this->ticket->id;
        $message->user_id = auth()->id();
        $message->sent_by = 'admin';
        $message->message = $this->message;
        $message->media_id = $mediaId;
        $message->seen_status = false;
        $message->save();

        // Reset form fields
        $this->message = '';
        $this->media = null;
        $this->mediaName = null;
        
        // Refresh ticket data
        $this->ticket->refresh();
        $this->ticket->load(['messages.media', 'user']);

        session()->flash('success', 'Reply sent successfully.');
    }

    public function toggleStatus()
    {
        $this->ticket->status = $this->ticket->status === 'closed' ? 'open' : 'closed';
        $this->ticket->save();
        
        $this->ticket->refresh();

        session()->flash('success', 'Ticket status updated successfully.');
        return redirect()->to(route('admin.dashboard.support'));
    }

    public function render()
    {
        return view('livewire.admin.ticket-view');
    }
  
}
