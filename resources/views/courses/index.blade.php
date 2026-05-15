@extends('layouts.app')

@section('title', 'All Courses — KoreSearch')

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-title">All Courses</h1>
        <p class="page-subtitle">Expand your knowledge with our expert-led courses</p>
    </div>
</div>

<div class="container">
    <div class="courses-layout">

        <aside class="filter-sidebar" id="filterSidebar">
            <div class="filter-header">
                <h3 class="filter-heading">Filters</h3>
                <button type="button" class="filter-close" id="filterClose" aria-label="Close filters">&times;</button>
            </div>

            <form method="GET" action="{{ route('courses.index') }}" id="filterForm">

                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <div class="search-input-wrap">
                        <svg class="search-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input
                            type="text"
                            name="search"
                            class="form-input"
                            placeholder="Title or description..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select name="category" class="form-select filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Level</label>
                    <div class="level-chips">
                        @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $val => $lbl)
                            <label class="level-chip {{ request('level') === $val ? 'active' : '' }}">
                                <input type="radio" name="level" value="{{ $val }}" {{ request('level') === $val ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <span>{{ $lbl }}</span>
                            </label>
                        @endforeach
                        @if(request('level'))
                            <a href="{{ removeQuery(['level']) }}" class="level-chip level-chip-clear">Clear</a>
                        @endif
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Price Range</label>
                    <div class="price-range">
                        <select name="price_min" class="form-select price-select">
                            <option value="">Min</option>
                            @foreach([0, 499, 999, 1499, 1999] as $p)
                                <option value="{{ $p }}" {{ request('price_min') == $p ? 'selected' : '' }}>৳{{ $p }}</option>
                            @endforeach
                        </select>
                        <span class="price-sep">to</span>
                        <select name="price_max" class="form-select price-select">
                            <option value="">Max</option>
                            @foreach([499, 999, 1499, 1999, 2999] as $p)
                                <option value="{{ $p }}" {{ request('price_max') == $p ? 'selected' : '' }}>৳{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Minimum Rating</label>
                    <div class="rating-chips">
                        @foreach([4, 3, 2] as $r)
                            <label class="rating-chip {{ request('rating') == $r ? 'active' : '' }}">
                                <input type="radio" name="rating" value="{{ $r }}" {{ request('rating') == $r ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <span>{{ $r }}+ <span class="star-icon">★</span></span>
                            </label>
                        @endforeach
                        @if(request('rating'))
                            <a href="{{ removeQuery(['rating']) }}" class="rating-chip rating-chip-clear">Any</a>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>

                @if(request()->hasAny(['search', 'category', 'level', 'price_min', 'price_max', 'rating']))
                    <a href="{{ route('courses.index') }}" class="btn btn-outline btn-block mt-sm">Clear All</a>
                @endif
            </form>
        </aside>

        <div class="courses-main">
            <div class="courses-toolbar">
                <div class="toolbar-left">
                    <button type="button" class="btn btn-outline btn-sm filter-toggle" id="filterToggle">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
                        Filters
                        @if(($count = collect(request()->only(['search','category','level','price_min','price_max','rating']))->filter()->count()) > 0)
                            <span class="filter-count-badge">{{ $count }}</span>
                        @endif
                    </button>
                    <p class="results-count">
                        Showing <strong>{{ $courses->firstItem() }}–{{ $courses->lastItem() }}</strong>
                        of <strong>{{ $courses->total() }}</strong> course{{ $courses->total() !== 1 ? 's' : '' }}
                    </p>
                </div>

                <div class="toolbar-right">
                    <select name="sort" class="form-select sort-select" form="filterForm" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low–High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
                    </select>
                </div>
            </div>

            @if(isset($activeFilters) && $activeFilters->isNotEmpty())
                <div class="active-filters">
                    @foreach($activeFilters as $filter)
                        <span class="filter-tag">
                            {{ $filter['label'] }}
                            <a href="{{ removeQuery([$filter['key']]) }}" class="filter-tag-remove">&times;</a>
                        </span>
                    @endforeach
                    <a href="{{ route('courses.index') }}" class="filter-tag-clear-all">Clear all</a>
                </div>
            @endif

            @if($courses->isEmpty())
                <div class="empty-state-box">
                    <div class="empty-state-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <p class="empty-state-title">No courses found</p>
                    <p class="empty-state-desc">Try adjusting your filters or search terms.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary">Browse All Courses</a>
                </div>
            @else
                <div class="courses-grid">
                    @foreach($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $courses->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var toggleBtn = document.getElementById('filterToggle');
    var sidebar = document.getElementById('filterSidebar');
    var closeBtn = document.getElementById('filterClose');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function() {
            sidebar.classList.remove('open');
        });
    }
})();
</script>
@endpush
