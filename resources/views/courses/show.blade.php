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

            <div class="rating-section">
                <h2 class="section-heading">Reviews</h2>

                @if($canRate)
                    <button class="btn btn-primary btn-rate-trigger" id="rateTrigger">
                        {{ $userRating ? 'Update Your Rating' : 'Rate This Course' }}
                    </button>
                @endif

                <div class="reviews-list">
                    @forelse($reviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-avatar">{{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}</span>
                                <div class="review-user">
                                    <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                                    <div class="review-stars">
                                        @for($s = 1; $s <= 5; $s++)
                                            <span class="review-star {{ $s <= $review->rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <span class="review-date">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            @if($review->review)
                                <p class="review-text">{{ $review->review }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="reviews-empty">No reviews yet. Be the first to review!</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

@if($canRate)
<div class="modal-overlay hidden" id="ratingModal">
    <div class="modal-content">
        <button class="modal-close" id="modalClose">&times;</button>
        <h2 class="modal-title">{{ $userRating ? 'Update Your Rating' : 'Rate This Course' }}</h2>
        <p class="modal-subtitle">{{ $course->title }}</p>

        <form method="POST" action="{{ route('courses.rate', $course) }}" class="rating-form" id="ratingForm">
            @csrf

            <div class="star-input-group">
                <div class="star-input" id="starInput">
                    @for($i = 5; $i >= 1; $i--)
                        <input
                            type="radio"
                            name="rating"
                            value="{{ $i }}"
                            id="modalStar{{ $i }}"
                            {{ $userRating && $userRating->rating == $i ? 'checked' : '' }}
                            required
                        >
                        <label for="modalStar{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
                    @endfor
                </div>
                <span class="star-input-label" id="modalStarLabel">
                    {{ $userRating ? 'Your rating: ' . $userRating->rating . '/5' : 'Click to rate' }}
                </span>
            </div>

            <div class="form-group">
                <label class="form-label" for="review">Write a review (optional)</label>
                <textarea
                    name="review"
                    id="review"
                    class="form-textarea"
                    rows="4"
                    maxlength="1000"
                    placeholder="Share your experience with this course..."
                >{{ $userRating ? $userRating->review : '' }}</textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="modalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $userRating ? 'Update' : 'Submit' }}</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
(function() {
    var modal = document.getElementById('ratingModal');
    var trigger = document.getElementById('rateTrigger');
    var close = document.getElementById('modalClose');
    var cancel = document.getElementById('modalCancel');
    var stars = document.querySelectorAll('#starInput label');
    var starLabel = document.getElementById('modalStarLabel');

    if (trigger && modal) {
        trigger.addEventListener('click', function() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }

    function closeModal() {
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    if (close) close.addEventListener('click', closeModal);
    if (cancel) cancel.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    }

    if (stars.length && starLabel) {
        stars.forEach(function(star) {
            star.addEventListener('mouseenter', function() {
                var val = this.getAttribute('for').replace('modalStar', '');
                starLabel.textContent = val + '/5';
            });

            star.addEventListener('mouseleave', function() {
                var checked = document.querySelector('#starInput input:checked');
                starLabel.textContent = checked
                    ? 'Your rating: ' + checked.value + '/5'
                    : 'Click to rate';
            });
        });
    }
})();
</script>
@endpush
