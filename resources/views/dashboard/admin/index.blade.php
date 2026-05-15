@extends('layouts.app')

@section('title', 'Admin Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.admin-sidebar')

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
                    <div class="stat-number">{{ $totalCourses }}</div>
                    <div class="stat-label">Total Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧾</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $totalOrders }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="{{ route('admin.users') }}" class="quick-action-card">
                <span class="qa-icon">👥</span>
                <span class="qa-title">Manage Users</span>
                <span class="qa-desc">Create, edit roles, manage passwords</span>
            </a>
            <a href="{{ route('admin.courses') }}" class="quick-action-card">
                <span class="qa-icon">📚</span>
                <span class="qa-title">Manage Courses</span>
                <span class="qa-desc">View and remove courses</span>
            </a>
            <a href="{{ route('admin.orders') }}" class="quick-action-card">
                <span class="qa-icon">🧾</span>
                <span class="qa-title">View Orders</span>
                <span class="qa-desc">Track all platform orders</span>
            </a>
        </div>

    </div>
</div>

@include('partials.dashboard-scripts')

@endsection
