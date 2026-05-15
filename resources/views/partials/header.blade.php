<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="{{ route('home') }}" class="navbar-brand">
            <span class="brand-icon">K</span>
            KoreSearch
        </a>

        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">Courses</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">Dashboard</a></li>
            @endauth
            <li>
                <a href="{{ route('cart.index') }}" class="cart-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    Cart
                    <span class="cart-badge" id="cartCount">{{ count(session()->get('cart', [])) }}</span>
                </a>
            </li>
            @guest
                <li><a href="{{ route('login') }}" class="btn-nav">Login</a></li>
                <li><a href="{{ route('register') }}" class="btn-nav btn-nav-accent">Register</a></li>
            @else
                <li class="nav-user" id="navUser">
                    <button class="user-avatar-btn" id="userAvatarBtn">
                        <span class="user-avatar-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="user-name-short">{{ Auth::user()->name }}</span>
                        <span class="dropdown-caret">▾</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <strong>{{ Auth::user()->name }}</strong>
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-logout">Logout</button>
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>

<script>
(function() {
    var avatarBtn = document.getElementById('userAvatarBtn');
    var dropdown = document.getElementById('userDropdown');
    var hamburger = document.getElementById('hamburgerBtn');
    var navLinks = document.getElementById('navLinks');

    if (avatarBtn && dropdown) {
        avatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', function() {
            dropdown.classList.remove('open');
        });
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
            navLinks.classList.toggle('open');
        });
    }
})();
</script>
