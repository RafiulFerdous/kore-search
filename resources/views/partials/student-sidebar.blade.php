<aside class="dashboard-sidebar" id="dashboardSidebar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">◀</button>
    <nav class="dash-nav">
        <a href="{{ route('student.dashboard') }}"
           class="dash-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <span class="dash-nav-icon">📊</span>
            <span class="dash-nav-label">Overview</span>
        </a>
        <a href="{{ route('student.courses') }}"
           class="dash-nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
            <span class="dash-nav-icon">📚</span>
            <span class="dash-nav-label">My Courses</span>
        </a>
        <a href="{{ route('student.orders') }}"
           class="dash-nav-link {{ request()->routeIs('student.orders') ? 'active' : '' }}">
            <span class="dash-nav-icon">🧾</span>
            <span class="dash-nav-label">Order History</span>
        </a>
    </nav>
</aside>
