<aside class="dashboard-sidebar" id="dashboardSidebar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">◀</button>
    <nav class="dash-nav">
        @foreach($navItems as $item)
            <a href="#{{ $item['section'] }}"
               class="dash-nav-link {{ $loop->first ? 'active' : '' }}"
               data-section="{{ $item['section'] }}">
                <span class="dash-nav-icon">{{ $item['icon'] }}</span>
                <span class="dash-nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
