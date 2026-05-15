@extends('layouts.app')

@section('title', 'Orders — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.admin-sidebar')

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Orders</h1>
            <p class="dash-subtitle">Track all platform orders</p>
        </div>

        <div class="dash-section">
            <h2 class="dash-section-title">All Orders ({{ $orders->total() }})</h2>
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
                {{ $orders->links('vendor.pagination.custom') }}
            </div>
        </div>

    </div>
</div>

@include('partials.dashboard-scripts')

@endsection
