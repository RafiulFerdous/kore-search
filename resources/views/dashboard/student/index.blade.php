@extends('layouts.app')

@section('title', 'Student Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.dashboard-sidebar', ['navItems' => [
        ['icon' => '📊', 'label' => 'Overview', 'section' => 'section-overview'],
        ['icon' => '📚', 'label' => 'My Courses', 'section' => 'section-courses'],
        ['icon' => '🧾', 'label' => 'Order History', 'section' => 'section-orders'],
    ]])

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
                    <div class="stat-number">{{ $recentOrders->count() }}</div>
                    <div class="stat-label">Orders</div>
                </div>
            </div>
        </div>

        <section class="dash-section" id="section-overview">
            <h2 class="dash-section-title">Quick Actions</h2>
            <div class="quick-actions">
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
                <a href="#section-courses" class="quick-action-card dash-nav-link" data-section="section-courses">
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
        </section>

        <section class="dash-section hidden" id="section-courses">
            <h2 class="dash-section-title">My Courses ({{ $courses->count() }})</h2>
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
                                <a href="#" class="btn btn-primary btn-sm btn-block mt-sm">Start Learning</a>
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
        </section>

        <section class="dash-section hidden" id="section-orders">
            <h2 class="dash-section-title">Order History</h2>
            @if($recentOrders->isNotEmpty())
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $order->course->title ?? '—' }}</td>
                                    <td>৳{{ number_format($order->amount) }}</td>
                                    <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>No orders yet. Start by enrolling in a course!</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-sm">Browse Courses</a>
                </div>
            @endif
        </section>

    </div>
</div>

@include('partials.dashboard-scripts')

@endsection
