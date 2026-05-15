@extends('layouts.app')

@section('title', 'Admin Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.dashboard-sidebar', ['navItems' => [
        ['icon' => '👥', 'label' => 'Users', 'section' => 'section-users'],
        ['icon' => '📚', 'label' => 'Courses', 'section' => 'section-courses'],
        ['icon' => '🧾', 'label' => 'Orders', 'section' => 'section-orders'],
    ]])

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Admin Dashboard</h1>
            <p class="dash-subtitle">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $courses->count() }}</div>
                    <div class="stat-label">Total Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧾</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $orders->count() }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
        </div>

        <section class="dash-section" id="section-users">
            <h2 class="dash-section-title">Users</h2>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dash-section hidden" id="section-courses">
            <h2 class="dash-section-title">Courses</h2>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>
                                    <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                                </td>
                                <td>{{ $course->instructor->name ?? '—' }}</td>
                                <td>{{ $course->category }}</td>
                                <td>{{ $course->isFree() ? 'Free' : '৳'.number_format($course->price) }}</td>
                                <td>{{ $course->enrolled_count }}</td>
                                <td>
                                    <span class="status-badge {{ $course->is_published ? 'status-completed' : 'status-pending' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dash-section hidden" id="section-orders">
            <h2 class="dash-section-title">Orders</h2>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Transaction #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $order->user->name ?? '—' }}</td>
                                <td>{{ $order->course->title ?? '—' }}</td>
                                <td class="transaction-number">{{ $order->transaction_number }}</td>
                                <td>৳{{ number_format($order->amount) }}</td>
                                <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-row">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

@include('partials.dashboard-scripts')

@endsection
