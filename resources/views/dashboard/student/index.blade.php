@extends('layouts.app')

@section('title', 'Student Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.student-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">Student Dashboard</h1>
            <p class="dash-subtitle">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $courses->count() }}</div>
                    <div class="stat-label">Courses Enrolled</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <div class="stat-number">৳{{ number_format($totalSpent) }}</div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $ordersCount }}</div>
                    <div class="stat-label">Orders</div>
                </div>
            </div>
        </div>

        <div class="quick-actions" style="margin-top:32px;">
            <a href="{{ route('courses.index') }}" class="quick-action-card">
                <span class="qa-icon">🔍</span>
                <span class="qa-title">Browse Courses</span>
                <span class="qa-desc">Discover new courses to enroll</span>
            </a>
            <a href="{{ route('cart.index') }}" class="quick-action-card">
                <span class="qa-icon">🛒</span>
                <span class="qa-title">View Cart</span>
                <span class="qa-desc">Check your saved courses</span>
            </a>
            <a href="{{ route('student.courses') }}" class="quick-action-card">
                <span class="qa-icon">📚</span>
                <span class="qa-title">My Learning</span>
                <span class="qa-desc">Continue your enrolled courses</span>
            </a>
        </div>

        @if($courses->isNotEmpty())
            <h3 class="dash-section-title" style="margin-top:32px;border-bottom:none;padding-bottom:0;">Continue Learning</h3>
            <div class="student-courses-grid">
                @foreach($courses->take(3) as $course)
                    <div class="student-course-card">
                        <img
                            src="{{ $course->thumbnail ?? 'https://placehold.co/300x180' }}"
                            alt="{{ $course->title }}"
                            class="student-course-thumb"
                            onerror="this.src='https://placehold.co/300x180'"
                        >
                        <div class="student-course-body">
                            <h4 class="student-course-title">{{ $course->title }}</h4>
                            <p class="student-course-instructor">{{ $course->instructor->name ?? 'KoreSearch' }}</p>
                            <span class="badge badge-{{ $course->level ?? 'beginner' }}">{{ ucfirst($course->level ?? 'beginner') }}</span>
                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary btn-sm btn-block mt-sm">View Course</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@include('partials.dashboard-scripts')
@endsection
