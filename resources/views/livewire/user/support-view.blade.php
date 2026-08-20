{{-- livewire/user/support-view.blade.php --}}
<div class="max-w-5xl mx-auto mt-8 p-6 bg-gray-50 rounded-2xl shadow">
    {{-- Header with ticket info --}}
    <div class="flex justify-between items-start">
        <img src="{{ asset('assets/images/support_chat_icon.png') }}" width="30" height="30">
        <div>
            <h2 class="text-xl font-semibold">{{ $ticket->reason_id }} | {{ $ticket->subject }}</h2>
            <p class="text-sm text-gray-500">Ticket: <span class="font-mono">{{ $ticket->ticket_id }}</span></p>
        </div>

        @php
            $latestMessage = $ticket->messages->last();
            $status = $ticket->status;
            $label = $status === 'closed' ? 'CLOSED' : ($latestMessage && $latestMessage->sent_by === 'user' ? 'OPEN PROCESSING' : 'AWAITING YOUR REPLY');
            $class = $status === 'closed' ? 'bg-danger' : ($latestMessage && $latestMessage->sent_by === 'user' ? 'bg-success' : 'bg-warning');
        @endphp
        <span class="px-2 py-1 rounded-sm text-white {{ $class }}">{{ $label }}</span>
    </div>

    <hr class="my-4 border-gray-200">

    {{-- Messages container --}}
    <div class="space-y-6">
        @foreach($ticket->messages as $message)
            @php
                $isUser = $message->sent_by === 'user';
                $sender = $isUser ? $ticket->user->first_name . ' ' . $ticket->user->last_name : 'Admin';
                $mediaUrl = $message->media_url ?? null;
            @endphp
            
            <div class="message {{ $isUser ? 'bg-blue-50 border-blue-200' : 'bg-gray-100 border-gray-200' }} p-4 rounded-lg border">
                <div class="flex justify-between text-sm mb-2">
                    <div class="font-semibold">{{ $sender }}</div>
                    <div class="text-gray-500">{{ $message->created_at->diffForHumans() }}</div>
                </div>
                
                <div class="message-content" wire:ignore>
                    @if($message->message)
                        <div class="message-text mb-3">{!! $message->message !!}</div>
                    @endif
                    
                    @if($mediaUrl)
                        <div class="message-media mt-2">
                            @if(Str::endsWith(strtolower($mediaUrl), ['.jpg', '.jpeg', '.png', '.gif']))
                                <img src="{{ $mediaUrl }}" alt="Attached image" class="max-w-xs rounded">
                            @else
                                <a href="{{ $mediaUrl }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    Download attachment
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reply form --}}
    <div class="mt-6">
        <form wire:submit.prevent="sendReply" enctype="multipart/form-data">
            <div class="border rounded-lg p-4 bg-white space-y-4">
                @if (session()->has('success'))
                    <div class="bg-green-50 text-green-800 p-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-50 text-red-800 p-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif
{{-- 
                <div 
                    x-data="{
                        initEditor() {
                            if (window.myEditor) {
                                window.myEditor.destroy().then(() => {
                                    this.createEditor();
                                });
                            } else {
                                this.createEditor();
                            }
                        },
                        createEditor() {
                            ClassicEditor
                                .create($refs.editor)
                                .then(editor => {
                                    window.myEditor = editor;
                                    editor.model.document.on('change:data', () => {
                                        $wire.set('message', editor.getData());
                                    });
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        }
                    }"
                    x-init="initEditor()"
                    x-on:clear-editor.window="initEditor()"
                    wire:ignore
                >
                    <textarea x-ref="editor" class="w-full h-32 border rounded p-2" placeholder="Type your message..."></textarea>
                </div> --}}

           {{-- Editor --}}
      {{-- Editor --}}
      <div>
        <textarea 
            wire:model.defer="message" 
            name="message" 
            id="message" 
            class="w-full h-32 border rounded p-2" 
            placeholder="Type your message...">
        </textarea>
    
        @error('message')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    
  
  
                        
            

                <div>
                    <label class="text-sm text-gray-600">Attach file (optional):</label>
                    <input type="file" wire:model="media" class="block w-full text-sm border rounded p-2">
                    <div wire:loading wire:target="media" class="text-sm text-gray-500 mt-1">
                        Uploading file...
                    </div>
                    @error('media') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror

                    @if($mediaPreview)
                        <div class="mt-2">
                            <img src="{{ $mediaPreview }}" class="max-h-32 rounded border">
                        </div>
                    @endif
                </div>

                <form wire:submit.prevent="sendReply">
                    <!-- message textarea + file input -->
                
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75"
                            wire:target="sendReply"
                        >
                            <span wire:loading.remove wire:target="sendReply">Submit</span>
                            <span wire:loading.delay wire:target="sendReply">Sending...</span>
                        </button>
                    </div>
                </form>
                
            </div>
        </form>
    </div>
</div>
