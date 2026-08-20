@extends('admin_layout.master')
@section('content')



<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-msg-body bg-white profile-shown">
                    
                    @livewire('admin.ticket-view', ['ticket' => $ticket])


                    <div class="nk-msg-profile visible" data-simplebar>
                        <div class="card">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <div class="user-card user-card-s2 mb-2">
                                        <div class="user-avatar md bg-primary">
                                            <span>{{ strtoupper(substr($ticket->user->first_name ?? 'ST', 0, 2)) }}</span>
                                        </div>
                                        <div class="user-info">
                                            <h5>{{ $ticket->user->first_name ?? 'USER' }}</h5>
                                            <span class="sub-text">USER</span>
                                        </div>
                                        <div class="user-card-menu dropdown">
                                            <a href="#" class="btn btn-icon btn-sm btn-trigger dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#"><em class="icon ni ni-eye"></em><span>View Profile</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-na"></em><span>Ban From System</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-repeat"></em><span>View Orders</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row text-center g-1">
                                        <div class="col-6">
                                            <div class="profile-stats">
                                                <span class="amount">{{ $ticket->user->orders()->count()}}</span>
                                                <span class="sub-text">Total Order</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="profile-stats">
                                                <span class="amount"><span class="amount">{{ $ticket->user->orders()->where('status', 1)->count() }}</span>                                            </span>
                                                <span class="sub-text">Complete</span>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="card-inner">
                                    <div class="aside-wg">
                                        <h6 class="overline-title-alt mb-2">User Information</h6>
                                        <ul class="user-contacts">
                                            <li>
                                                <em class="icon ni ni-mail"></em><span>{{ $ticket->user->email}}</span>
                                            </li>
                                          
                                            <li>
                                                <em class="icon ni ni-map-pin"></em><span>{{ $ticket->user->addresses()->first()->address ?? 'N/A'}} <br>{{ $ticket->user->addresses()->first()->city ?? 'N/A' }} , {{ $ticket->user->addresses()->first()->postal_code ?? 'N/A'}} </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="aside-wg">
                                        <h6 class="overline-title-alt mb-2">Additional</h6>
                                        <div class="row gx-1 gy-3">
                                            <div class="col-6">
                                                <span class="sub-text">Ref ID: </span>
                                                <span>{{ $ticket->ticket_id }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="sub-text">Requested:</span>
                                                <span>{{ $ticket->user->first_name}} {{ $ticket->user->last_name}}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="sub-text">Status:</span>
                                                @if($ticket->status == "open")
                                                <span class="lead-text text-success">Open</span>
                                                @else
                                                <span class="lead-text text-danger">Closed</span>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                @php
                                                $lastReply = $ticket->messages->sortByDesc('created_at')->first();
                                            @endphp
                                            
                                            <span class="sub-text">Last Reply:</span>
                                            <span>
                                                @if($lastReply)
                                                    @if($lastReply->sent_by === 'user')
                                                        {{ $ticket->user->first_name ?? 'User' }}
                                                    @else
                                                        Support Team
                                                    @endif
                                                @else
                                                    No replies yet
                                                @endif
                                            </span>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                            </div>
                        </div>
                    </div><!-- .nk-msg-profile -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('livewire:load', function () {
        // Handle bootstrap modals with Livewire
        Livewire.hook('message.processed', (message, component) => {
            // Re-initialize Bootstrap tooltips after Livewire updates
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    });
</script>