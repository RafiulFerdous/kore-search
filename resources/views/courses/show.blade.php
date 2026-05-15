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
                    <div class="star-rating" style="--score: {{ $course->rating }}">
                        <span class="stars-outer"><span class="stars-inner"></span></span>
                        <span class="rating-value">{{ number_format($course->rating, 1) }}</span>
                        <span class="rating-count">({{ $course->ratings_count ?? 0 }})</span>
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

                    @php $inCart = $course->isInCart(); @endphp
                    <div class="course-show-actions">
                        <button
                            class="btn btn-accent btn-block btn-add-cart{{ $inCart ? ' in-cart' : '' }}"
                            data-course-id="{{ $course->id }}"
                            {{ $inCart ? 'disabled' : '' }}
                        >
                            @if($inCart)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Added to Cart
                            @else
                                Add to Cart
                            @endif
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

            @if($canRate)
            <div class="rating-section">
                <h2 class="section-heading">Rate This Course</h2>

                <form method="POST" action="{{ route('courses.rate', $course) }}" class="rating-form">
                    @csrf

                    <div class="star-input-group">
                        <div class="star-input" id="starInput">
                            @for($i = 5; $i >= 1; $i--)
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $i }}"
                                    id="star{{ $i }}"
                                    {{ $userRating && $userRating->rating == $i ? 'checked' : '' }}
                                    {{ $userRating ? 'disabled' : '' }}
                                >
                                <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
                            @endfor
                        </div>
                        <span class="star-input-label" id="starLabel">
                            {{ $userRating ? 'Your rating: ' . $userRating->rating . '/5' : 'Click to rate' }}
                        </span>
                    </div>

                    @if($userRating)
                        <p class="rating-thanks">You rated this course {{ $userRating->rating }}/5. Thank you!</p>
                    @else
                        <button type="submit" class="btn btn-primary">Submit Rating</button>
                    @endif
                </form>
            </div>
            @endif

        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var stars = document.querySelectorAll('.star-input label');
    var label = document.getElementById('starLabel');

    if (stars.length && label) {
        stars.forEach(function(star) {
            star.addEventListener('mouseenter', function() {
                var val = this.getAttribute('for').replace('star', '');
                label.textContent = val + '/5';
            });

            star.addEventListener('mouseleave', function() {
                var checked = document.querySelector('.star-input input:checked');
                label.textContent = checked
                    ? 'Your rating: ' + checked.value + '/5'
                    : 'Click to rate';
            });
        });
    }
})();
</script>
@endpush
