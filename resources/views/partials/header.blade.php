<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="{{ route('home') }}" class="navbar-brand">
            <span class="brand-icon">K</span>
            <span class="brand-text">KoreSearch</span>
        </a>

        <ul class="nav-links" id="navLinks">
            <li class="nav-drawer-header">
                <span class="nav-drawer-title">Menu</span>
                <button class="nav-drawer-close" id="drawerCloseBtn" aria-label="Close menu">&times;</button>
            </li>
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">Courses</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('*.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            @endauth
            <li>
                <a href="{{ route('cart.index') }}" class="cart-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <svg class="cart-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Cart
                    <span class="cart-badge" id="cartCount">{{ $cartCount }}</span>
                </a>
            </li>
            @guest
                <li class="nav-mobile-only"><a href="{{ route('login') }}" class="btn-nav">Login</a></li>
                <li class="nav-mobile-only"><a href="{{ route('register') }}" class="btn-nav btn-nav-accent">Register</a></li>
            @endguest
            @auth
                <li class="nav-user-li nav-mobile-only">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-user-li nav-mobile-only">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-logout mobile-logout">Logout</button>
                    </form>
                </li>
            @endauth
        </ul>

        <div class="nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn-nav btn-nav-desktop">Login</a>
                <a href="{{ route('register') }}" class="btn-nav btn-nav-accent btn-nav-desktop">Register</a>
            @endguest
            @auth
                <div class="nav-user" id="navUser">
                    <button class="user-avatar-btn" id="userAvatarBtn">
                        <span class="user-avatar-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="user-name-short">{{ Auth::user()->name }}</span>
                        <svg class="dropdown-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <strong>{{ Auth::user()->name }}</strong>
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <a href="{{ route('courses.index') }}">Browse Courses</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-logout">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
            <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>
<div class="mobile-overlay" id="mobileOverlay"></div>

<script>
(function() {
    var avatarBtn = document.getElementById('userAvatarBtn');
    var dropdown = document.getElementById('userDropdown');
    var hamburger = document.getElementById('hamburgerBtn');
    var navLinks = document.getElementById('navLinks');
    var overlay = document.getElementById('mobileOverlay');
    var navbar = document.getElementById('navbar');

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

    function closeMenu() {
        navLinks.classList.remove('open');
        hamburger.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
            navLinks.classList.toggle('open');
            hamburger.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
        });
        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }
    }

    var drawerClose = document.getElementById('drawerCloseBtn');
    if (drawerClose) {
        drawerClose.addEventListener('click', closeMenu);
    }

    var lastScroll = 0;
    if (navbar) {
        window.addEventListener('scroll', function() {
            var current = window.scrollY;
            if (current > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            if (current > lastScroll && current > 80) {
                navbar.classList.add('hidden');
            } else {
                navbar.classList.remove('hidden');
            }
            lastScroll = current;
        }, { passive: true });
    }
})();
</script>
