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
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Get Started Free</a>
            </div>
            <div class="hero-stats">
                @if($hero?->stats)
                    @foreach($hero->stats as $stat)
                        <div class="hero-stat">
                            <strong>{{ $stat['count'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="hero-stat">
                        <strong>540+</strong>
                        <span>Students</span>
                    </div>
                    <div class="hero-stat">
                        <strong>20+</strong>
                        <span>Courses</span>
                    </div>
                    <div class="hero-stat">
                        <strong>10+</strong>
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
            @foreach($categories as $category)
                <a href="{{ route('courses.index', ['category' => $category]) }}" class="category-chip">
                    <span class="category-icon">
                        @if($category === 'Backend') 🖥️
                        @elseif($category === 'Frontend') 🎨
                        @elseif($category === 'Database') 🗄️
                        @elseif($category === 'Design') ✏️
                        @else 📚
                        @endif
                    </span>
                    {{ $category }}
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

<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Start Learning Today</h2>
            <p>Join thousands of students already growing their skills on KoreSearch.</p>
            <a href="{{ route('register') }}" class="btn btn-accent btn-lg">Create Free Account</a>
        </div>
    </div>
</section>

@endsection
