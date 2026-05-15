@extends('layouts.app')

@section('title', 'My Courses — Student | KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.student-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">My Courses</h1>
            <p class="dash-subtitle">Courses you are enrolled in</p>
        </div>

        @if($courses->isNotEmpty())
            <div class="student-courses-grid">
                @foreach($courses as $course)
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
                            <div class="student-course-meta">
                                <span class="badge badge-{{ $course->level ?? 'beginner' }}">{{ ucfirst($course->level ?? 'beginner') }}</span>
                                @if($course->rating)
                                    <span class="course-rating-stars">⭐ {{ number_format($course->rating, 1) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary btn-sm btn-block mt-sm">View Course</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>You haven't enrolled in any courses yet.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary mt-sm">Browse Courses</a>
            </div>
        @endif
    </div>
</div>

@include('partials.dashboard-scripts')
@endsection
