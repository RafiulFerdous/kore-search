@extends('layouts.app')

@section('title', 'KoreSearch — Learn From The Best')

@section('content')

<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <h1 class="hero-title">{!! $hero?->title ?? 'Unlock Your Potential with <span class="highlight">KoreSearch</span>' !!}</h1>
            <p class="hero-subtitle">{{ $hero?->subtitle ?? 'Explore expert-led courses in development, design, and technology. Build the skills employers are looking for — at your own pace.' }}</p>
            <div class="hero-actions">
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Browse Courses</a>
                @guest
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Get Started Free</a>
                @endguest
            </div>
            <div class="hero-stats">
                @if($hero?->stats)
                    @foreach($hero->stats as $stat)
                        <div class="hero-stat">
                            <strong><span class="hero-stat-count">{{ $stat['count'] }}</span></strong>
                            <span>{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="hero-stat">
                        <strong><span class="hero-stat-count">{{ $totalStudents }}</span>+</strong>
                        <span>Students</span>
                    </div>
                    <div class="hero-stat">
                        <strong><span class="hero-stat-count">{{ $totalCourses }}</span>+</strong>
                        <span>Courses</span>
                    </div>
                    <div class="hero-stat">
                        <strong><span class="hero-stat-count">{{ $totalInstructors }}</span>+</strong>
                        <span>Instructors</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="hero-image">
            <img src="{{ $hero?->hero_image ?? 'https://placehold.co/560x400/1F3864/ffffff?text=Learn+Online' }}" alt="Learn Online with KoreSearch">
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Find the right course for your career goals</p>
        </div>
        <div class="categories-grid">
            @php
                $catMeta = [
                    'Backend'  => ['icon' => '🖥️', 'color' => '#1F3864', 'light' => '#D6E4F0'],
                    'Frontend' => ['icon' => '🎨', 'color' => '#C62828', 'light' => '#FFEBEE'],
                    'Database' => ['icon' => '🗄️', 'color' => '#2E7D32', 'light' => '#E8F5E9'],
                    'Design'   => ['icon' => '✏️', 'color' => '#F57F17', 'light' => '#FFF8E1'],
                    'DevOps'   => ['icon' => '⚙️', 'color' => '#6B21A8', 'light' => '#F3E8FF'],
                ];
            @endphp
            @foreach($categories as $i => $category)
                @php $meta = $catMeta[$category] ?? ['icon' => '📚', 'color' => '#64748B', 'light' => '#F1F5F9']; @endphp
                <a href="{{ route('courses.index', ['category' => $category]) }}" class="category-card" style="--cat-color: {{ $meta['color'] }}; --cat-light: {{ $meta['light'] }}; --cat-i: {{ $i }};">
                    <span class="category-icon-wrap">{{ $meta['icon'] }}</span>
                    <span class="category-name">{{ $category }}</span>
                    <span class="category-arrow">→</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="courses-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Courses</h2>
            <a href="{{ route('courses.index') }}" class="section-link">View all courses →</a>
        </div>
        <div class="courses-grid">
            @forelse($featuredCourses as $course)
                <x-course-card :course="$course" />
            @empty
                <p class="empty-state">No courses available yet. Check back soon!</p>
            @endforelse
        </div>
    </div>
</section>

@guest
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Start Learning Today</h2>
            <p>Join thousands of students already growing their skills on KoreSearch.</p>
            <a href="{{ route('register') }}" class="btn btn-accent btn-lg">Create Free Account</a>
        </div>
    </div>
</section>
@endguest

@endsection
