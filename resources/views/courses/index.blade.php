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

        <aside class="filter-sidebar">
            <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <input
                        type="text"
                        name="search"
                        class="form-input"
                        placeholder="Search courses..."
                        value="{{ request('search') }}"
                    >
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
                    <select name="level" class="form-select filter-select">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>

                @if(request()->hasAny(['search', 'category', 'level']))
                    <a href="{{ route('courses.index') }}" class="btn btn-outline btn-block mt-sm">Clear Filters</a>
                @endif
            </form>
        </aside>

        <div class="courses-main">
            <div class="courses-toolbar">
                <p class="results-count">{{ $courses->total() }} course{{ $courses->total() !== 1 ? 's' : '' }} found</p>
            </div>

            @if($courses->isEmpty())
                <div class="empty-state-box">
                    <p>No courses found matching your filters.</p>
                    <a href="{{ route('courses.index') }}">Browse all courses</a>
                </div>
            @else
                <div class="courses-grid">
                    @foreach($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
