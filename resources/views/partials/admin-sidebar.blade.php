<aside class="dashboard-sidebar" id="dashboardSidebar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">◀</button>
    <nav class="dash-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="dash-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="dash-nav-icon">📊</span>
            <span class="dash-nav-label">Overview</span>
        </a>
        <a href="{{ route('admin.users') }}"
           class="dash-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="dash-nav-icon">👥</span>
            <span class="dash-nav-label">Users</span>
        </a>
        <a href="{{ route('admin.courses') }}"
           class="dash-nav-link {{ request()->routeIs('admin.courses*') && !request()->routeIs('admin.courses.destroy') ? 'active' : '' }}">
            <span class="dash-nav-icon">📚</span>
            <span class="dash-nav-label">Courses</span>
        </a>
        <a href="{{ route('admin.orders') }}"
           class="dash-nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <span class="dash-nav-icon">🧾</span>
            <span class="dash-nav-label">Orders</span>
        </a>

        <div class="dash-nav-group">
            <button class="dash-nav-link dash-nav-toggle {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" id="settingsToggle">
                <span class="dash-nav-icon">⚙️</span>
                <span class="dash-nav-label">Settings</span>
                <span class="dash-nav-caret">▾</span>
            </button>
            <div class="dash-subnav {{ request()->routeIs('admin.settings.*') ? 'open' : '' }}" id="settingsSubnav">
                <a href="{{ route('admin.settings.hero') }}"
                   class="dash-subnav-link {{ request()->routeIs('admin.settings.hero') ? 'active' : '' }}">
                    <span class="dash-nav-icon">🏠</span>
                    <span class="dash-nav-label">Hero Setting</span>
                </a>
                <a href="{{ route('admin.settings.featured') }}"
                   class="dash-subnav-link {{ request()->routeIs('admin.settings.featured') ? 'active' : '' }}">
                    <span class="dash-nav-icon">⭐</span>
                    <span class="dash-nav-label">Featured Courses</span>
                </a>
            </div>
        </div>
    </nav>
</aside>

<script>
(function() {
    var toggle = document.getElementById('settingsToggle');
    var subnav = document.getElementById('settingsSubnav');
    if (toggle && subnav) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            subnav.classList.toggle('open');
            toggle.classList.toggle('active');
        });
    }
})();
</script>
