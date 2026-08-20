
@php
    use App\Models\EmailRecoveryPassword;
@endphp

<!DOCTYPE html>
<html lang="en" class="js">

<head>
    @livewireStyles

    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Favicon-legalio/Favicon.png') }}">
    <!-- Page Title  -->
    <title>Legalio | Admin Dashboard</title>
    <!-- StyleSheets  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/adminstyle.css') }}?time={{ time() }}">
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css') }}?time={{ time() }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('admin-theme/assets/css/theme.css?ver=3.1.2') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @yield('script')

    @viteReactRefresh
    @vite('resources/js/app.jsx')


</head>


<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-menu-trigger">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                        <!-- <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a> -->
                    </div>
                    <div class="nk-sidebar-brand">
                        <a href="{{ url('admin-dashboard') }}" class="logo-link nk-sidebar-logo">
                            <!-- <h3>Legalio</h3> -->
                            <img class="logo-light logo-img" src="{{ asset('assets/img/logo.svg') }}" srcset="{{ asset('assets/img/logo.svg') }}" alt="logo">
                            <img class="logo-dark logo-img" src="{{ asset('assets/img/logo.svg') }}" srcset="{{ asset('assets/img/logo.svg') }}" alt="logo-dark">
                        </a>
                    </div>
                </div><!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-item">
                                    <a href="{{ url('/admin-dashboard') }}" class="nk-menu-link ">
                                        <span class="nk-menu-icon"><em class="icon ni ni-growth"></em></span>
                                        <span class="nk-menu-text">Dashboard</span>
                                    </a>
                                </li>
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                                        <span class="nk-menu-text">Orders</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.orders') }}" class="nk-menu-link"><span class="nk-menu-text">All Transactions</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.subscription.plans') }}" class="nk-menu-link"><span class="nk-menu-text">Subscription Plans</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.discount') }}" class="nk-menu-link"><span class="nk-menu-text">Discount</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">Users</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/users') }}" class="nk-menu-link"><span class="nk-menu-text">All Users</span></a>
                                        </li>

                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-star"></em></span>
                                        <span class="nk-menu-text">Reviews</span>
                                    </a>
                                    <ul class="nk-menu-sub review-page">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.published_reviews') }}" class="nk-menu-link">  <span class="nk-menu-icon">
                                                <img  src="{{ asset('assets/admin_img_icon/Uploaded.svg') }}" alt="Published Reviews Icon">
                                            </span><span class="nk-menu-text">Published Reviews</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.pending_reviews') }}" class="nk-menu-link"><span class="nk-menu-icon">
                                                <img  src="{{ asset('assets/admin_img_icon/Pending.svg') }}"></span><span class="nk-menu-text">Pending Reviews</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.reviews') }}" class="nk-menu-link"><span class="nk-menu-icon">
                                                <img  src="{{ asset('assets/admin_img_icon/ADD.svg') }}"></span><span class="nk-menu-text">Add Reviews</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.config.reviews') }}" class="nk-menu-link"><span class="nk-menu-icon">
                                                <img  src="{{ asset('assets/admin_img_icon/Configuration.svg') }}"></span><span class="nk-menu-text">Configuration</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->is_admin == 1 || auth()->user()->is_admin == 2)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-file"></em></span>
                                        <span class="nk-menu-text">Documents</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        @if(auth()->user()->is_admin == 1 || auth()->user()->is_admin == 2)
                                         <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.documents.beta') }}" class="nk-menu-link"><span class="nk-menu-text">Documents</span></a>
                                        </li>
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.documents') }}" class="nk-menu-link"><span class="nk-menu-text">Documents</span></a>
                                        </li> --}}
                                         @if(auth()->user()->is_admin == 1)
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.document.standard_section') }}" class="nk-menu-link"><span class="nk-menu-text">Standard Clauses</span></a>
                                        </li>
                                        @endif

                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.parties-templates') }}" class="nk-menu-link"><span class="nk-menu-text">Parties Templates</span></a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->is_admin == 1)
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/document/categories') }}" class="nk-menu-link"><span class="nk-menu-text">Categories</span></a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->is_admin == 1 || auth()->user()->is_admin == 2)
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/general-section') }}" class="nk-menu-link"><span class="nk-menu-text">General Section</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.article_section') }}" class="nk-menu-link"><span class="nk-menu-text">Article Section</span></a>
                                        </li>
                                        {{-- <li class="nk-menu-item">
                                            <a href="{{ route('index') }}" class="nk-menu-link"><span class="nk-menu-text">State-Specific Clauses</span></a>
                                        </li> --}}
                                        @endif
                                        {{-- @if(auth()->user()->is_admin == 1)
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.document.standard_section') }}" class="nk-menu-link"><span class="nk-menu-text">Standard Clauses</span></a>
                                        </li>
                                        @endif --}}
                                    </ul>
                                </li>
                                @endif
                                
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-files"></em></span>
                                        <span class="nk-menu-text">Pages</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/home-content') }}" class="nk-menu-link"><span class="nk-menu-text">Home</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/how-it-works') }}" class="nk-menu-link"><span class="nk-menu-text">How It Works</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/faq') }}" class="nk-menu-link"><span class="nk-menu-text">FAQ's</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/faq-category') }}" class="nk-menu-link"><span class="nk-menu-text">FAQ's Categories</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/terms-and-conditions') }}" class="nk-menu-link"><span class="nk-menu-text">Terms & Condition</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/help-center') }}" class="nk-menu-link"><span class="nk-menu-text">Help Center</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/privacy-policy') }}" class="nk-menu-link"><span class="nk-menu-text">Privacy Policy</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/legal-notice') }}" class="nk-menu-link"><span class="nk-menu-text">Legal Notice</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/contact-us') }}" class="nk-menu-link"><span class="nk-menu-text">Contact Us</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/who-we-are') }}" class="nk-menu-link"><span class="nk-menu-text">Who We Are</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/login') }}" class="nk-menu-link"><span class="nk-menu-text">Iniciar sesión</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/register') }}" class="nk-menu-link"><span class="nk-menu-text">Crear cuenta</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/prices') }}" class="nk-menu-link"><span class="nk-menu-text">Prices</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/legal-document') }}" class="nk-menu-link"><span class="nk-menu-text">Legal Document</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-grid-alt"></em></span>
                                        <span class="nk-menu-text">Globals</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/header') }}" class="nk-menu-link"><span class="nk-menu-text">Header</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/footer') }}" class="nk-menu-link"><span class="nk-menu-text">Footer</span></a>
                                        </li>
                                       
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.global.configuration') }}" class="nk-menu-link"><span class="nk-menu-text">Configuration</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-cc-alt2"></em></span>
                                        <span class="nk-menu-text">AI Prompts</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('all.prompt') }}" class="nk-menu-link"><span class="nk-menu-text">All Prompts</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('add.prompt') }}" class="nk-menu-link"><span class="nk-menu-text">Add Prompt</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.docx_prompts') }}" class="nk-menu-link"><span class="nk-menu-text">Document Prompts</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('verification.prompt') }}" class="nk-menu-link"><span class="nk-menu-text">Prompt Verification</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('ai.config') }}" class="nk-menu-link"><span class="nk-menu-text">Configuration</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ route('doc.prompts') }}" class="nk-menu-link"><span class="nk-menu-text">Document Generating Prompts</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-mail"></em></span>
                                        <span class="nk-menu-text">Emails</span>
                                    </a>
                                    <ul class="nk-menu-sub">

                                        @php
                                            $emails = EmailRecoveryPassword::all();
                                        @endphp

                                        @foreach ($emails as $email)
                                            <li class="nk-menu-item">
                                                <a href="{{ route('admin.dashboard.recovery.password.email', ['type' => $email->email_type]) }}" class="nk-menu-link">
                                                    <span class="nk-menu-text">{{ $email->email_name }}</span>
                                                </a>
                                            </li>
                                        @endforeach

                                    </ul>
                                </li>
                                @endif
                                
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                                        <span class="nk-menu-text">Notifications</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.notifications') }}" class="nk-menu-link"><span class="nk-menu-text">Notifications</span></a>
                                        </li>

                                    </ul>
                                </li>
                                @endif
                                {{-- @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-card-view"></em></span>
                                        <span class="nk-menu-text">Knowledge Base</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                         <li class="nk-menu-item">
                                            <a href="{{route('knowledge.base.category')}}" class="nk-menu-link"><span class="nk-menu-text">Categories</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{route('knowledge.base.article')}}" class="nk-menu-link"><span class="nk-menu-text">Articles</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif --}}
                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-chat"></em></span>
                                        <span class="nk-menu-text">AI Assistance</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                         <li class="nk-menu-item">
                                            <a href="{{route('admin.dashboard.ai.FAQ')}}" class="nk-menu-link"><span class="nk-menu-text">Add FAQs</span></a>
                                        </li>

                                        {{-- **************** --}}
                                        
                                        <li class="nk-menu-item">
                                            <a href="{{route('admin.dashboard.ai.FAQ.tags')}}" class="nk-menu-link"><span class="nk-menu-text">Tags</span></a>
                                        </li>

                                        <li class="nk-menu-item">
                                            <a href="{{ route('admin.dashboard.ai.pending.FAQ') }}" class="nk-menu-link"><span class="nk-menu-text">Answer FAQs</span></a>
                                        </li>
                                        
                                        {{-- **************** --}}
                                    </ul>
                                    

                                </li>
                                @endif

                                @if(auth()->user()->is_admin == 1 || auth()->user()->is_admin == 3)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-chat-circle-fill"></em></span>
                                        <span class="nk-menu-text">Support Tickets</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                         <li class="nk-menu-item">
                                            <a href="{{route('admin.dashboard.support')}}" class="nk-menu-link"><span class="nk-menu-text">List</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->is_admin == 1)
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                                        <span class="nk-menu-text">Configuration</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/web-setting') }}" class="nk-menu-link"><span class="nk-menu-text">Configuration</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="{{ url('/admin-dashboard/messages') }}" class="nk-menu-link"><span class="nk-menu-text">Messages</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @endif


                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->
            </div>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">

                        <div class="nk-sidebar-element nk-sidebar-head nk-header-lft">
                            <div class="nk-menu-trigger">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                                    <!-- <em class="icon ni ni-arrow-left"></em> -->
                                    <em class="icon ni ni-menu"></em>
                                </a>
                                <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"></a>
                            </div>
                            <div class="nk-sidebar-brand">
                                <a href="http://127.0.0.1:8000/admin-dashboard" class="logo-link nk-sidebar-logo">
                                    <!-- <h3>Legalio</h3> -->
                                    <img class="logo-light logo-img" src="http://127.0.0.1:8000/assets/img/logo.svg" srcset="http://127.0.0.1:8000/assets/img/logo.svg" alt="logo">
                                    <img class="logo-dark logo-img" src="http://127.0.0.1:8000/assets/img/logo.svg" srcset="http://127.0.0.1:8000/assets/img/logo.svg" alt="logo-dark">
                                </a>
                            </div>
                        </div>

                        <div class="nk-header-wrap">
                            <div class="nk-header-brand d-xl-none">
                            </div>
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                                <?php
                                                    $user = App\Models\User::where('is_admin',1)->first();
                                                ?>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status">Administrator</div>
                                                    <div class="user-name dropdown-indicator">{{ $user->name ?? '' }}</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span>{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->first_name ?? '' }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Change Password --}}
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ route('admin.dashboard.change.password') }}"><em class="icon ni ni-lock"></em><span>Manage Password</span></a></li>
                                                </ul>
                                            </div>
                                            {{-- end change pasword --}}

                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ url('/admin-logout') }}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li><!-- .dropdown -->
                                </ul><!-- .nk-quick-nav -->
                            </div><!-- .nk-header-tools -->
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                <!-- main header @e -->

                @yield('content')

                <!-- <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; 2024-2025 Legal Documents</a>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- select region modal -->

    <!-- JavaScript -->
    <script src="{{ asset('admin-theme/assets/js/bundle.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/scripts.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/charts/gd-default.js?ver=3.1.2') }}"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin-theme/assets/js/adminscript.js') }}?time={{ time() }}"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    @if(Session::get('error'))
    <script>
        Swal.fire({
            title: "Error!",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#d33"
        });
    </script>
    @endif
    @if(Session::get('success'))
    <script>
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK",
            confirmButtonColor: "#FD5602"
        });
    </script>
    @endif


    <script>
        document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.global-delete-link').forEach(element => {
            element.addEventListener('click', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Check if it's a form button
                        if (element.tagName.toLowerCase() === 'button' && element.form) {
                            element.form.submit();
                        } else if (element.tagName.toLowerCase() === 'a') {
                            window.location.href = element.getAttribute('href');
                        }
                    }
                });
            });
        });
        });

    </script>

    <script>
        $(document).ready(function() {
            $('#tags').select2({
                placeholder: "Select tags",
                allowClear: true
            });
        });
    </script>


    @livewireScripts
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <script src="{{ asset('assets/admin/document-generator-beta.js') }}"></script>

</body>

</html>
