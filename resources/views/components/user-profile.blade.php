<div class="dropdown-main">
    <a href="{{ route('user.profile') }}">
        <div class="user_detail">
            <div class="user_img">
                <img class="finalUploadedImage"src="{{ optional(auth()->user())->profile_image ?? dimage() }}">
                <!-- {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }} -->
            </div>
            <div class="user_name">
                <h5>{{ Auth::user()->first_name }}</h5>
                <p>{{ Auth::user()->email }}</p>
            </div>
        </div>
    </a>
    <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fa fa-file-text"></i>
            {{-- Mi cuenta --}}
            My Account
        </a>
    </div>

    <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.profile') }}"><i class="fa fa-user"></i>
            {{-- Mi perfil --}}
            My Profile
        </a>
    </div>

    <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.configuration') }}"><i class="fa fa-cog"></i>
            {{-- Cambiar
            contraseña --}}
            Change Password
        </a>
    </div>

    <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.invoice') }}"><i
                class="fa-solid fa-envelope-open-text"></i>
                {{-- Recibos y facturas --}}
                Receipts & Invoices
            </a>
    </div>

    <div class="dash-icon">
    <a class="dropdown-item" href="{{ route('subscription.details') }}">
        <i class="fa-solid fa-credit-card"></i>
            Subscription
        </a>
    </div>

    <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.review') }}"><i class="fa-solid fa-clipboard"></i>
            {{-- Mis reseñas --}}
            My Reviews
        </a>
    </div>

    {{-- <div class="dash-icon">
        <a class="dropdown-item" href="{{ route('user.support') }}"><i class="fa-solid fa-headset"></i>
            Support
        </a>
    </div> --}}

    <div class="dash-icon">
        <a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i>
            {{-- Salir --}}
            Log Out
        </a>
    </div>
</div>

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Cannot Delete Account',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    });
</script> 
@endif