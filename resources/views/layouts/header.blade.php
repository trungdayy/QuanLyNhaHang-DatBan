<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
    <a href="{{ route('home') }}" class="navbar-brand p-0">
        <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Buffet Ocean</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0 pe-4">

            <a href="{{ route('home') }}" class="nav-item nav-link {{ Route::is('home') ? 'active' : '' }}">
                Home
            </a>
            <a href="{{ route('about') }}" class="nav-item nav-link {{ Route::is('about') ? 'active' : '' }}">
                About
            </a>
            <a href="{{ route('service') }}" class="nav-item nav-link {{ Route::is('service') ? 'active' : '' }}">
                Service
            </a>
            <a href="{{ route('combos.index') }}"
                class="nav-item nav-link {{ Route::is('combos.index') || Route::is('combos.*') ? 'active' : '' }}">
                Combo Buffet
            </a>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ Route::is('contact') ? 'active' : '' }}">
                Contact
            </a>

        </div>
        <a href="{{ route('booking.index') }}" class="btn btn-primary py-2 px-4">Đặt Bàn</a>
    </div>
</nav>
