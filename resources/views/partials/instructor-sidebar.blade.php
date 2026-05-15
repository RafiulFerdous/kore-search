<aside class="dashboard-sidebar" id="dashboardSidebar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">◀</button>
    <nav class="dash-nav">
        <a href="{{ route('instructor.dashboard') }}"
           class="dash-nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
            <span class="dash-nav-icon">📊</span>
            <span class="dash-nav-label">Overview</span>
        </a>
        <a href="{{ route('instructor.courses') }}"
           class="dash-nav-link {{ request()->routeIs('instructor.courses*') && !request()->routeIs('instructor.courses.destroy') ? 'active' : '' }}">
            <span class="dash-nav-icon">📚</span>
            <span class="dash-nav-label">My Courses</span>
        </a>
    </nav>
</aside>
