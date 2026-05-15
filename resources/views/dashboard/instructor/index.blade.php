@extends('layouts.app')

@section('title', 'Instructor Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.instructor-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">Instructor Dashboard</h1>
            <p class="dash-subtitle">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $courses->count() }}</div>
                    <div class="stat-label">My Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $totalStudents }}</div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <div class="stat-number">৳{{ number_format($totalRevenue) }}</div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>
        </div>

        <div class="quick-actions" style="margin-top:32px;">
            <a href="{{ route('instructor.courses') }}" class="quick-action-card">
                <span class="qa-icon">📚</span>
                <span class="qa-title">My Courses</span>
                <span class="qa-desc">View and manage your courses</span>
            </a>
            <a href="{{ route('courses.index') }}" class="quick-action-card">
                <span class="qa-icon">🔍</span>
                <span class="qa-title">Browse Courses</span>
                <span class="qa-desc">See all courses on the platform</span>
            </a>
        </div>
    </div>
</div>

@include('partials.dashboard-scripts')
@endsection
