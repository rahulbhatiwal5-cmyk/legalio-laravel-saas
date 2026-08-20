@php
    $result = App\Models\MetaData::where('key', 'user_dash_header_logo')->first();
    $user_header_logo = str_replace('public/', '', $result->file_path ?? null);

    $keys = [
        'footer_logo',
        'footer_copyright',
        'footer_text',
        'header_btn_1',
    ];
    $setting = App\Models\MetaData::whereIn('key', $keys)->get()->keyBy('key');

    $data = [
        'footer_logo' => str_replace('public/', '', $setting['footer_logo']->file_path ?? null),
        'footer_copyright' => $setting['footer_copyright']->value ?? null,
        'footer_text' => $setting['footer_text']->value ?? null,
        'button1' => $setting['header_btn_1']->value ?? null,
    ];
@endphp


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Favicon-legalio/Favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css"
        integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"
        integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    <!-- Cropper.js CSS for enabling image cropping styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">


    @livewireStyles


    {{-- csrf token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <title>userDashboard</title> -->
    <link rel="stylesheet" href="{{ asset('assets/css/user_dashboard/usercustom.css') }}?time={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/user_dashboard/userresponsive.css') }}?time={{ time() }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    @yield('title')

</head>

<body>


    <header class="main_dhdr">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="hdr_lft">
                    {{-- <a class="navbar-brand" href="{{ route('user.home') }}"> --}}
                        <a class="navbar-brand" href="{{ auth()->check() ? url('/legal-documents') : url('/') }}">
                            <img src="{{ asset('storage/' . $user_header_logo) ?? '' }}" class="img-fluid" alt="">
                        </a>
                        <button class="menu-toggler" style="display: none;">
                            <span class="bar bar1"></span>
                            <span class="bar bar2"></span>
                            <span class="bar bar3"></span>
                        </button>
                </div>

                <div class="hdr_ryt user-icon">
                    <div class="hdr_info">
                        <livewire:user-document-search>
                            @php
                                $unread = auth()->user()->unreadNotifications()->limit(10)->get();
                                $all = auth()->user()->notifications()->limit(10)->get();
                            @endphp

                            <div class="hedaer_bnt">
                                <div class="all_doc_btn">
                                    <a href="{{ route('user.all__documents') }}"
                                        class="cta_dark">{{ $data['button1'] ?? 'Crear documento' }}</a>
                                </div>

                                <div class="hdr_rytrgt ">
                                    <div class="notf drop_menu">
                                        <a class="notfictn_lnk">
                                            <img src="{{ asset('assets/img/bell_img.png') }}" class="img-fluid">
                                            <span class="badge custom-badge badge-success">{{ $unread->count() }}</span>
                                        </a>
                                        @auth
                                            <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right"
                                                style=" min-width: 360px !important;max-width: 360px !important;border-radius:20px !important; margin-right: 20px; min-width: 320px;">
                                                <div class="dropdown-main">
                                                    <div class="row"
                                                        style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2; width: 100%; margin: 0 !important;">
                                                        <div class="col-12 text-left">
                                                            All Notification
                                                        </div>
                                                    </div>
                                                    <div style="overflow-y: auto; overflow-x: hidden;height:250px;">
                                                        <div class="row"
                                                            style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2; width: 100%; margin: 0 !important;">
                                                            <div class="col-2 showAll"> <span
                                                                    class="sub-title nk-dropdown-title ">All </span></div>
                                                            <div class="col-2 showUnread" style="text-align: left;"> <span
                                                                    class="sub-title nk-dropdown-title ">Unread({{ $unread->count() }})
                                                                </span></div>
                                                            <div class="col-8" style="text-align: right;">
                                                                <form action="{{ route('notifications.readAll') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <button class="all-read" style="
                                                            background: transparent;
                                                            border: none;
                                                            color:#012555;
                                                        " type="submit">Mark all as read</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <small class="p-3 pt-2">Today</small>
                                                            </div>
                                                        </div>
                                                        <div class="allNotification">
                                                            @forelse($all as $notification)
                                                                <div class="row"
                                                                    style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2; width: 100%; margin: 0 !important;">
                                                                    <div class="col-2">
                                                                        <div
                                                                            class="bg-light rounded-circle p-2 text-warning text-center">
                                                                            <img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg"
                                                                                alt="Notification Icon" width="24" height="24">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-7">
                                                                        <span><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong></span>

                                                                    </div>
                                                                    <div class="col-3">
                                                                        @php
                                                                            $hours = $notification->created_at->diffForHumans(null, true);
                                                                        @endphp

                                                                        <small>
                                                                            @if ($hours < 1)
                                                                                Just now
                                                                            @elseif ($hours === 1)
                                                                                1 hr ago
                                                                            @else
                                                                                {{ $hours }}
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="text-muted text-center">No notifications</div>
                                                            @endforelse
                                                        </div>

                                                        <div class="unRead d-none"
                                                            style="overflow-y: auto; overflow-x: hidden;height:200px;">
                                                            @forelse($unread as $notification)
                                                                <div class="row"
                                                                    style="font-size: 0.8125rem; padding: 14px; border-bottom: 1px solid #e5e9f2; width: 100%; margin: 0 !important;">
                                                                    <div class="col-1">
                                                                        <div class="col-2">
                                                                            <div
                                                                                class="bg-light rounded-circle p-2 text-warning text-center">
                                                                                <img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg"
                                                                                    alt="Notification Icon" width="24"
                                                                                    height="24">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-8">
                                                                        <span>
                                                                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @php
                                                                            $hours = $notification->created_at->diffForHumans(null, true);
                                                                        @endphp

                                                                        <small>
                                                                            @if ($hours < 1)
                                                                                Just now
                                                                            @elseif ($hours === 1)
                                                                                1 hr ago
                                                                            @else
                                                                                {{ $hours }}
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="text-muted text-center">No unread notifications
                                                                </div>
                                                            @endforelse

                                                        </div>
                                                    </div>
                                                    <div class="row row text-center p-2 fs-7 text-muted">
                                                        <div class="col-12">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endauth

                                    </div>
                                    <div class="user_img drop_menu">
                                        <div class="usr_profile">
                                            <img class="finalUploadedImage"
                                                src="{{ optional(auth()->user())->profile_image ?? dimage() }}">
                                        </div>

                                        <div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">

                                            
                                            <x-user-profile />
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                    </div>
                </div>
        </div>
        </nav>
        </div>
    </header>


    <section class="user_dashbord">

        <div class="row">
            <div class="col-lg-3 p-0">
                <div class="dashboard_lft">
                    <div class="left-text">
                        <ul class="list-unstyled dash-tab mb-0" id="menu">
                            <li class="nav-links">
                                <a href="{{ route('user.dashboard') }}" {{--
                                    class="nav-link  {{ request()->is('user-dashboard') ? 'active' : '' }}"> --}}
                                    class="nav-link {{ request()->is('account') ? 'active' : '' }}">

                                    <div class="side-links">
                                        <span class="icons-links frst_icn">
                                            <img src="{{ asset('assets/img/Group.svg') }}">
                                        </span>
                                        <span class="icons-text">
                                            {{-- Mi cuenta --}}
                                            My Account
                                        </span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                <a href="{{ route('user.saved') }}" {{--
                                    class="nav-link nav_sv {{ request()->is('user-dashboard/saved') ? 'active' : '' }}">
                                    --}}
                                    {{-- class="nav-link nav_sv {{ request()->is('account/saved') ? 'active' : '' }}">
                                    --}}
                                    class="nav-link nav_sv {{ request()->is('account/drafts') ? 'active' : '' }}">

                                    <div class="side-links">
                                        <span class="icons-links scnd_icn">
                                            <img src="{{ asset('assets/img/Union.svg') }}">
                                        </span>
                                        <span class="icons-text">
                                            {{-- Guardados --}}
                                            Drafts
                                        </span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                <a href="{{ route('user.purchased') }}" {{--
                                    class="nav-link  {{ request()->is('user-dashboard/purchased') ? 'active' : '' }}">
                                    --}}
                                    class="nav-link {{ request()->is('account/purchased') ? 'active' : '' }}">

                                    <div class="side-links">
                                        <span class="icons-links thrd_icn">
                                            <img src="{{ asset('assets/img/Group36546-New.svg') }}">
                                        </span>
                                        <span class="icons-text">
                                            {{-- Comprados --}}
                                            My Documents
                                        </span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-links">
                                {{-- <a href="{{route('user.review')}}"> --}}
                                    <a href="{{ route('user.ai.assistant') }}" {{--
                                        class="nav-link  {{ request()->is('user-dashboard/ai-assistant') ? 'active' : '' }}">
                                        --}}
                                        {{-- class="nav-link {{ request()->is('account/ai-assistant') ? 'active' : ''
                                        }}"> --}}
                                        class="nav-link {{ request()->is('account/assistant') ? 'active' : '' }}">

                                        <div class="side-links">
                                            <span class="icons-links">
                                                <img src="{{ asset('assets/img/Group_36547.svg') }}">
                                            </span>
                                            <span class="icons-text">
                                                {{-- Asistente --}}
                                                Assistant
                                            </span>
                                        </div>
                                    </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 p-0">
                <div class="user_content @if(request()->segment(2) == 'ai-assistant') user_chat_content @endif">
                    <div class="user_content_inner_wrap">
                        <div class="user_content_inner">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>



    <footer class="di_ftr">
        <div class="outer_foot_bg dark cstm-ftr">
            <div class="container">
                <div class="foot_end_box">
                    <div class="reserve_box">
                        <!-- Copyright ©2025 Documentos Legal. Reservados todos los derechos. -->
                        {{ str_replace('{current_year}', date('Y'), $data['footer_copyright'] ?? '') }}
                    </div>
                    <div class="reserve_box custom_links">
                        {{-- <a href="{{ url('/terminos-y-condiciones') }}" class="custom_li">Términos y Condiciones</a>
                        <a href="{{ url('/aviso-de-privacidad') }}" class="custom_li">Aviso de Privacidad</a> --}}
                        {{-- <a href="{{ url('/terminos-y-condiciones') }}" class="custom_li">Terms & Conditions</a>
                        --}}
                        <a href="{{ url('/terms-conditions') }}" class="custom_li">Terms & Conditions</a>
                        {{-- <a href="{{ url('/aviso-de-privacidad') }}" class="custom_li">Privacy Policy</a> --}}
                        <a href="{{ url('/privacy-policy') }}" class="custom_li">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
        integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('assets/js/userscript.js') }}"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3" defer></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>

    @stack('scripts')

    <script>
        AOS.init();
    </script>
    <script>
        $(document).ready(function () {
            $('.showUnread').on('click', function (event) {
                event.stopPropagation();
                $('.allNotification').addClass('d-none');
                $('.unRead').removeClass('d-none');
                $('.unRead').addClass('active');
            });
            $('.showAll').on('click', function (event) {
                event.stopPropagation();
                $('.allNotification').removeClass('d-none');
                $('.unRead').addClass('d-none');
                $('.allNotification').addClass('active');
            })
        });
    </script>
    {{--
    <script>
        let editorInstance;

        function initializeCKEditor() {
            const editorElement = document.querySelector('#ticket_message_editor');
            if (!editorElement || editorElement.classList.contains('ck-loaded')) return;

            ClassicEditor
                .create(editorElement, {
                    toolbar: ['heading', 'bold', 'bulletedList', 'numberedList'],
                    heading: {
                        options: [
                            { model: 'heading2', view: 'h2', title: 'Heading 2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4' }
                        ]
                    },
                    removePlugins: ['Table', 'MediaEmbed', 'BlockQuote'],
                })
                .then(editor => {
                    editorInstance = editor;

                    editor.model.document.on('change:data', () => {
                        const html = editor.getData();
                        document.getElementById('ticket_message').value = html;
                        // Dispatch a native input event to notify Livewire
                        document.getElementById('ticket_message').dispatchEvent(new Event('input'));
                    });

                    editorElement.classList.add('ck-loaded');
                })
                .catch(error => {
                    console.error('CKEditor init error:', error);
                });
        }

        document.addEventListener('livewire:load', () => {
            initializeCKEditor();
        });

        Livewire.hook('message.processed', (message, component) => {
            console.log('Livewire message processed, initializing CKEditor');
            initializeCKEditor();
        });
    </script> --}}

</body>

</html>