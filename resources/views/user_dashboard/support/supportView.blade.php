@extends('user_dashboard_layout.master')

@section('content')

@livewire('user.support-view', ['ticketId' => $ticket->ticket_id])

@endsection




