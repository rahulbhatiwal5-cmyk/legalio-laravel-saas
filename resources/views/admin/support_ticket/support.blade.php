@extends('admin_layout.master')
@section('content')


                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">

                                    <div class="card card-bordered h-100">
                                        <div class="card-inner border-bottom">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Support Requests</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <a href="#" class="link">All Tickets</a>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="nk-support">
                                            @foreach ($tickets as $ticket)
                                            @php
                                                $isUser = $ticket->messages->first()->sent_by === 'user';
                                                $senderName = $isUser ? $ticket->user?->first_name ?? 'Guest' . ' ' . $ticket->user?->last_name  ?? 'Guest': 'Admin';

                                            @endphp
                                            <li class="nk-support-item">
                                                <div class="user-avatar">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($senderName) }}&size=32"
                                                        alt="{{ $senderName }}"
                                                        class="w-6 h-6 rounded-full object-cover">
                                                </div>
                                                <div class="nk-support-content">
                                                    <div class="title">
                                                        <span>{{$ticket->user?->first_name ?? 'guest'}}</span>
                                                        <div class="flex items-center space-gap-3">
                                                            @if($ticket->status == "open")
                                                            <span class="badge badge-dot badge-dot-xs bg-warning ms-1">Pending</span>
                                                            @else
                                                            <span class="badge badge-dot badge-dot-xs bg-success ms-1">Closed</span>
                                                            @endif

                                                            <a href="{{ route('admin.dashboard.support.view',['id'=>$ticket->ticket_id]) }}" type="button" class="Button">View</a>
                                                        </div>
                                                    </div>

                                                    <p>{{$ticket->subject}}</p>
                                                    <span class="time">{{ $ticket->created_at->diffForHumans() }}</span>
                                                </div>
                                            </li>
                                            @endforeach


                                        </ul>
                                    </div><!-- .card -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->

</div>
@endsection
