<div>
    <div class="nk-msg-head">
        <h4 class="title d-none d-lg-block">{{ $ticket->subject }}</h4>
        <div class="nk-msg-head-meta">
            <div class="d-none d-lg-block">
                <ul class="nk-msg-tags">
                    <li><span class="label-tag"><em class="icon ni ni-flag-fill"></em> <span>{{ $ticket->reason_id }}</span></span></li>
                </ul>
            </div>
            <div class="d-lg-none"><a href="#" class="btn btn-icon btn-trigger nk-msg-hide ms-n1"><em class="icon ni ni-arrow-left"></em></a></div>
            <ul class="nk-msg-actions">
                <a href="#" 
                    class="btn btn-dim btn-sm btn-outline-light"
                    wire:click.prevent="toggleStatus">
                     <em class="icon ni ni-check"></em>
                     @if($ticket->status === 'closed')
                         <span class="text-danger">Closed</span>
                     @else
                         <span class="text-success">Mark as Closed</span>
                     @endif
                </a>
                <li class="d-lg-none"><a href="#" class="btn btn-icon btn-sm btn-white btn-light profile-toggle"><em class="icon ni ni-info-i"></em></a></li>
                <li class="dropdown">
                    <a href="#" class="btn btn-icon btn-sm btn-white btn-light dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li><a href="#"><em class="icon ni ni-user-add"></em><span>Assign To Member</span></a></li>
                            <li><a href="#"><em class="icon ni ni-archive"></em><span>Move to Archive</span></a></li>
                            <li><a href="#" wire:click.prevent="toggleStatus"><em class="icon ni ni-done"></em><span>Mark as Close</span></a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <a href="#" class="nk-msg-profile-toggle profile-toggle active"><em class="icon ni ni-arrow-left"></em></a>
    </div><!-- .nk-msg-head -->
    
    <div class="nk-msg-reply nk-reply" data-simplebar>
        <div class="nk-msg-head py-4 d-lg-none">
            <h4 class="title">{{ $ticket->subject }}</h4>
            <ul class="nk-msg-tags">
                <li><span class="label-tag"><em class="icon ni ni-flag-fill"></em> <span>{{ $ticket->reason_id }}</span></span></li>
            </ul>
        </div>

        @php
            $lastDate = null;
        @endphp
    
        @foreach($ticket->messages as $message)
            @php
                $currentDate = $message->created_at->format('d F Y');
            @endphp
        
            @if($currentDate !== $lastDate)
                <div class="nk-reply-meta">
                    <div class="nk-reply-meta-info">
                        <strong>{{ $currentDate }}</strong>
                    </div>
                </div>
                @php $lastDate = $currentDate; @endphp
            @endif
        
            <div class="nk-reply-item">
                <div class="nk-reply-header">
                    <div class="user-card">
                        @if($message->sent_by === 'user')
                        <div class="user-avatar sm bg-blue">
                            <span>{{ strtoupper(substr($ticket->user->first_name ?? 'ST', 0, 2)) }}</span>
                        </div>
                        @else
                        <div class="user-avatar sm bg-purple">
                            <span>{{'AD'}}</span>
                        </div>
                        @endif
                        <div class="user-name">
                            @if($message->sent_by === 'user')
                                {{ $ticket->user->first_name ?? 'User' }}
                                <span class="text-xs text-muted">
                                    ({{ strtoupper(substr($ticket->user->first_name ?? 'US', 0, 2)) }})
                                </span>
                            @else
                                Support Team
                                <span class="text-xs text-muted">(Support)</span>
                            @endif
                        </div>
                    </div>
                    <div class="date-time">{{ $message->created_at->format('d M, Y h:i A') }}</div>
                </div>
        
                <div class="nk-reply-body">
                    <div class="nk-reply-entry entry">
                        {!! $message->message !!}
                    </div>
        
                    @php
                        $media = !empty($message->media) ? $message->media : null;
                    @endphp
                    
                    @if(!empty($media))
                        <div class="attach-files mt-2">
                            <ul class="attach-list">
                                <li class="attach-item">
                                    @if(Str::startsWith($media->file_format, 'image/'))
                                    <a href="javascript:void(0);" onclick="window.dispatchEvent(new CustomEvent('openImagePreview-{{ $media->id }}'))">
                                        <em class="icon ni ni-img"></em>
                                        <span>{{ $media->file_name }}</span>
                                    </a>
                                    
                                    @else
                                        <a class="download" href="{{ asset('storage/' . $media->directory_name . '/' . $media->file_name) }}" target="_blank">
                                            <em class="icon ni ni-clip-v"></em>
                                            <span>{{ $media->file_name }}</span>
                                        </a>
                                    @endif
                                </li>
                            </ul>
                            <div class="attach-foot">
                                <span class="attach-info">1 file attached</span>
                                <a class="attach-download link" href="{{ asset('storage/' . $media->directory_name . '/' . $media->file_name) }}" target="_blank">
                                    <em class="icon ni ni-download"></em>
                                    <span>Download</span>
                                </a>
                            </div>
                        </div>
                    
                        {{-- Modal for image preview --}}
                        @if(Str::startsWith($media->file_format, 'image/'))
                            <div
                                x-data="{ open: false, imageUrl: '{{ asset('storage/' . $media->directory_name . '/' . $media->file_name) }}', title: '{{ $media->file_name }}' }"
                                x-init="window.addEventListener('openImagePreview-{{ $media->id }}', () => open = true)"
                            >
                                <div x-show="open" class="modal-backdrop fade show"></div>
                                <div x-show="open" :class="{ 'show': open }" class="modal fade" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <button type="button" class="close" @click="open = false" aria-label="Close">
                                                <em class="icon ni ni-cross"></em>
                                            </button>
                        
                                            <div class="modal-header">
                                                <h5 class="modal-title" x-text="title"></h5>
                                            </div>
                        
                                            <div class="modal-body text-center">
                                                <img :src="imageUrl" class="img-fluid" alt="Media Preview">
                                            </div>
                        
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" @click="open = false">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    
                    @endif
                </div>
            </div>
        @endforeach
    
        <div class="nk-reply-form">
            <div class="nk-reply-form-header">
                <ul class="nav nav-tabs-s2 nav-tabs nav-tabs-sm">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#reply-form">Reply</a>
                    </li>
                </ul>
                <div class="nk-reply-form-title">
                    <div class="title">Reply as:</div>
                    <div class="user-avatar xs bg-purple">
                        <span>AD</span>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="adminReply">
                <div class="tab-content">
                    <div class="tab-pane active" id="reply-form">
                        <div class="nk-reply-form-editor">
                            <div class="nk-reply-form-field">
                                <textarea wire:model="message" class="form-control form-control-simple no-resize" placeholder="Type your reply here..."></textarea>
                                @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="nk-reply-form-tools">
                                <ul class="nk-reply-form-actions g-1">
                                    <li class="me-2">
                                        <button class="btn btn-primary" type="submit">Reply</button>
                                    </li>
                                    <li>
                                        <label for="mediaUpload" class="btn btn-icon btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload Attachment">
                                            <em class="icon ni ni-clip-v"></em>
                                        </label>
                                        <input type="file" id="mediaUpload" wire:model="media" class="d-none">
                                    </li>
                                    
                                    @if($mediaName)
                                    <li>
                                        <span class="text-success">File selected: {{ $mediaName }}</span>
                                    </li>
                                    @endif
                                </ul>
                                @error('media') <span class="text-danger">{{ $message }}</span> @enderror
                                
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle btn-trigger btn btn-icon me-n2" data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-v"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><span>Another Option</span></a></li>
                                            <li><a href="#"><span>More Option</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-reply-form-tools -->
                        </div><!-- .nk-reply-form-editor -->
                    </div>
                </div>
            </form>
        </div><!-- .nk-reply-form -->
    </div><!-- .nk-reply -->
    
 
</div>