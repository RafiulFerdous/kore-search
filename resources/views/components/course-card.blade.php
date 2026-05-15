@props(['course'])

<div class="course-card">
    <a href="{{ route('courses.show', $course->slug) }}" class="course-card-thumb-link">
        <img
            src="{{ $course->thumbnail ?? 'https://placehold.co/800x450' }}"
            alt="{{ $course->title }}"
            class="course-card-thumb"
            onerror="this.src='https://placehold.co/800x450'"
            loading="lazy"
        >
        <div class="course-card-overlay"></div>
        <span class="course-level-badge badge-{{ $course->level }}">{{ ucfirst($course->level) }}</span>
        @if($course->isFree())
            <span class="course-price-ribbon">Free</span>
        @else
            <span class="course-price-ribbon">৳{{ number_format($course->price) }}</span>
        @endif
    </a>
    <div class="course-card-body">
        <div class="course-card-top">
            <span class="course-category-tag">{{ $course->category }}</span>
            <span class="course-duration">{{ $course->duration ?? '—' }}</span>
        </div>
        <h3 class="course-card-title">
            <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
        </h3>
        <p class="course-card-instructor">
            by {{ $course->instructor->name ?? 'KoreSearch Instructor' }}
        </p>
        <div class="course-card-meta">
            <div class="star-rating" style="--score: {{ $course->rating }}">
                <span class="stars-outer">
                    <span class="stars-inner"></span>
                </span>
                <span class="rating-value">{{ number_format($course->rating, 1) }}</span>
            </div>
            <span class="meta-dot">•</span>
            <span class="enrolled-count">{{ number_format($course->enrolled_count) }} students</span>
        </div>
        <div class="course-card-footer">
            <button class="btn-add-cart" data-course-id="{{ $course->id }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Add to Cart
            </button>
            <a href="{{ route('courses.show', $course->slug) }}" class="btn-view-link" title="View details">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>
    </div>
</div>
