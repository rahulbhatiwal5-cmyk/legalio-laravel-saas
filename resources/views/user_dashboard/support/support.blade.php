@extends('user_dashboard_layout.master')
@section('content')
    <div class="uer_nm">
        <h1>Soporte</h1>
    </div>

    {{-- <div class="crt_main" id="append_document">
        No saved documents found
    </div> --}}
    <div class="scroll_div">
        @foreach ($tickets as $ticket )
        <div class="cart_dv">
            <div class="crt_lft">
                <div class="cart_img">
                    <img src="{{ asset('assets/images/support_chat_icon.png') }}" class="img-fluid" height="30px" width="30px">
                </div>
                <div class="cart_text">
                    <h4>{{$ticket->reason_id}}: {{$ticket->subject}}</h4>
                    <p>{{$ticket->ticket_id}}   </p>
                </div>
                @php
                // Check latest message and ticket status
                $latestMessage = $ticket->messages->last();
                $ticketStatus = $ticket->status;
            
                $statusLabel = '';
                $statusClass = '';
                
                if ($ticketStatus === 'closed') {
                    $statusLabel = 'CLOSED';
                    $statusClass = 'bg-danger text-white';
                } elseif ($latestMessage && $latestMessage->sent_by === 'user') {
                    $statusLabel = 'OPEN PROCESSING';
                    $statusClass = 'bg-success text-white';
                } elseif ($latestMessage && $latestMessage->sent_by === 'admin') {
                    $statusLabel = 'AWAITING YOUR REPLY';
                    $statusClass = 'bg-warning text-white';
                }
            @endphp
            
            <span class="px-2 py-1 rounded-sm {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
            
            </div>
            <div class="crt_ryt">
                <div class="datt_text">
                
                    <p>{{ $ticket->created_at->diffForHumans() }}</p>
                </div>
            
            
                    <a class="btn btn-primary" type="button" href="{{ route('user.support.view',['id'=>$ticket->ticket_id]) }}">View Ticket</a>
                
            
            <div class="shr_dt dot" style="display: none">
                    
                    <div class="dropdown-menu_review">
                        <div class="user_name">
                            <p class="text-center">Manage Document</p>
                        </div>
                        <div class="dropdown-main">
                            <!-- <div class="dash-icon">
                                <a class="dropdown-item" href="#"><i class="fa-brands fa-slack"></i> Logo Details</a>
                            </div>
                            <div class="dash-icon">
                                <a class="dropdown-item" href="#"><i class="fa-solid fa-download"></i> Download Logo</a>
                            </div>
                            <div class="dash-icon">
                                <a class="dropdown-item" href="#"><i class="fas fa-wallet"></i> Customization</a>
                            </div>
                            <div class="dash-icon">
                                <a class="dropdown-item" href="#"><i class="fa-solid fa-envelope-open-text"></i> Manage Logo Backup</a>
                            </div> -->
                            {{-- <div class="dash-icon">
                                <a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i>Edit</a>
                            </div> --}}
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        @endforeach
    </div>

@endsection