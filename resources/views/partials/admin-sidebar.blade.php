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
    </nav>
</aside>
