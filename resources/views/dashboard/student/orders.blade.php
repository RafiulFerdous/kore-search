@extends('layouts.app')

@section('title', 'Order History — Student | KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.student-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">Order History</h1>
            <p class="dash-subtitle">Your past purchases</p>
        </div>

        @if($orders->isNotEmpty())
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
                        @foreach($orders as $order)
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
            <div class="pagination-wrap">
                {{ $orders->links() }}
            </div>
        @else
            <div class="empty-state">
                <p>No orders yet. Start by enrolling in a course!</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary mt-sm">Browse Courses</a>
            </div>
        @endif
    </div>
</div>

@include('partials.dashboard-scripts')
@endsection
