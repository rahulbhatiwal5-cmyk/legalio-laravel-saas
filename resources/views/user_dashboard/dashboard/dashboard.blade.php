@extends('user_dashboard_layout.master')
@section('content')


    <div class="uer_nm">
        <h1>
            {{-- Mi cuenta --}}
            My Account
        </h1>
    </div>
    <div class="scroll_div">
        <div class="mi_detail">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <a class="acc-box" href="{{route('user.saved')}}">
                        <div class="acc-img">
                            <img src="{{ asset('assets/img/saved.svg') }}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                            <h2>
                                {{-- Guardados --}}
                                Drafts
                            </h2>
                            <p>Access your documents in progress.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6"  >
                    <a class="acc-box" href="{{ route('user.purchased') }}">
                        <div class="acc-img">
                            <img src="{{ asset('assets/img/purchase.svg') }}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                            <h2>
                                {{-- Comprados --}}
                                My Documents
                            </h2>
                            <p>View all documents you’ve generated.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6" >
                    <a class="acc-box" href="{{ route('user.ai.assistant') }}">
                        <div class="acc-img">
                            <img src="{{ asset('assets/img/review.svg') }}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                            <h2>
                                {{-- Asistente --}}
                                Assistant
                            </h2>
                            <p>Get help from the assistant.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6"  >
                    {{-- <a class="acc-box" href="{{route('help.center')}}"> --}}
                        <a class="acc-box">
                        <div class="acc-img">
                            <img src="{{ asset('assets/img/configuration.svg') }}" class="img-fluid">
                        </div>
                        <div class="acc-text">
                            <h2>
                                {{-- Ayuda --}}
                                Help
                            </h2>
                            <p>Find answers and support.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
