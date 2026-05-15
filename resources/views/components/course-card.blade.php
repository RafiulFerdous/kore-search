@props(['course'])

<div class="course-card">
    <a href="{{ route('courses.show', $course->slug) }}" class="course-card-thumb-link">
        <img
            src="{{ $course->thumbnail ?? 'https://placehold.co/800x450' }}"
            alt="{{ $course->title }}"
            class="course-card-thumb"
            onerror="this.src='https://placehold.co/800x450'"
        >
        <span class="course-level-badge badge-{{ $course->level }}">{{ ucfirst($course->level) }}</span>
    </a>
    <div class="course-card-body">
        <span class="course-category-tag">{{ $course->category }}</span>
        <h3 class="course-card-title">
            <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
        </h3>
        <p class="course-card-instructor">
            by {{ $course->instructor->name ?? 'KoreSearch Instructor' }}
        </p>
        <div class="course-card-meta">
            <div class="star-rating" style="--rating: {{ $course->rating }}">
                <span class="stars-outer">
                    <span class="stars-inner"></span>
                </span>
                <span class="rating-value">{{ number_format($course->rating, 1) }}</span>
            </div>
            <span class="enrolled-count">{{ number_format($course->enrolled_count) }} students</span>
        </div>
        <div class="course-card-footer">
            @if($course->isFree())
                <span class="course-price free">Free</span>
            @else
                <span class="course-price">৳{{ number_format($course->price) }}</span>
            @endif
            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-primary">View Course</a>
        </div>
    </div>
</div>
