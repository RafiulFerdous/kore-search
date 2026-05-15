@extends('layouts.app')

@section('title', $course->title . ' — KoreSearch')

@section('content')

<div class="course-show-hero">
    <div class="container">
        <div class="course-show-header">
            <div class="course-show-info">
                <span class="course-category-tag">{{ $course->category }}</span>
                <h1 class="course-show-title">{{ $course->title }}</h1>
                <p class="course-show-desc">{{ $course->description }}</p>

                <div class="course-show-meta">
                    <div class="star-rating" style="--rating: {{ $course->rating }}">
                        <span class="stars-outer"><span class="stars-inner"></span></span>
                        <span class="rating-value">{{ number_format($course->rating, 1) }}</span>
                    </div>
                    <span class="meta-sep">·</span>
                    <span>{{ number_format($course->enrolled_count) }} students enrolled</span>
                    <span class="meta-sep">·</span>
                    <span class="badge badge-{{ $course->level }}">{{ ucfirst($course->level) }}</span>
                </div>

                <p class="course-instructor-line">
                    Created by <strong>{{ $course->instructor->name ?? 'KoreSearch Instructor' }}</strong>
                </p>
            </div>

            <div class="course-show-card">
                <img
                    src="{{ $course->thumbnail ?? 'https://placehold.co/800x450' }}"
                    alt="{{ $course->title }}"
                    class="course-show-thumb"
                    onerror="this.src='https://placehold.co/800x450'"
                >
                <div class="course-show-card-body">
                    @if($course->isFree())
                        <div class="course-show-price free">Free</div>
                    @else
                        <div class="course-show-price">৳{{ number_format($course->price) }}</div>
                    @endif

                    <div class="course-show-actions">
                        <button
                            class="btn btn-accent btn-block btn-add-cart"
                            data-course-id="{{ $course->id }}"
                        >
                            Add to Cart
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block mt-sm">
                            Go to Cart
                        </a>
                    </div>

                    <ul class="course-includes">
                        <li>🕒 {{ $course->duration ?? 'Self-paced' }}</li>
                        <li>👥 {{ number_format($course->enrolled_count) }} enrolled</li>
                        <li>📶 {{ ucfirst($course->level) }} level</li>
                        <li>🏅 Certificate of completion</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="course-show-body">

        <div class="course-show-main">

            <div class="curriculum-section">
                <h2 class="section-heading">Course Curriculum</h2>
                <p class="section-note">This course contains {{ count($course->topics ?? []) }} topics. Video content will be available after enrollment.</p>

                <ul class="curriculum-list">
                    @forelse($course->topics ?? [] as $index => $topic)
                        <li class="curriculum-item">
                            <span class="curriculum-lock">🔒</span>
                            <span class="curriculum-number">{{ $index + 1 }}.</span>
                            <span class="curriculum-title">{{ $topic }}</span>
                        </li>
                    @empty
                        <li class="curriculum-item">
                            <span>No topics listed yet.</span>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="instructor-section">
                <h2 class="section-heading">About the Instructor</h2>
                <div class="instructor-card">
                    <div class="instructor-avatar">
                        {{ strtoupper(substr($course->instructor->name ?? 'K', 0, 1)) }}
                    </div>
                    <div class="instructor-info">
                        <h3>{{ $course->instructor->name ?? 'KoreSearch Instructor' }}</h3>
                        <p>{{ $course->instructor->headline ?? 'Expert Instructor at KoreSearch' }}</p>
                        <p class="instructor-bio">{{ $course->instructor->bio ?? 'Experienced professional sharing knowledge through KoreSearch.' }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
