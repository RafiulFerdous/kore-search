@extends('layouts.app')

@section('title', 'Your Cart — KoreSearch')

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Your Cart</h1>
    </div>
</div>

<div class="container">
    @if($courses->isEmpty())
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Browse our courses and add something you'd like to learn.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">Browse Courses</a>
        </div>
    @else
        <div class="cart-layout">
            <div class="cart-items">
                <h2 class="cart-heading">{{ $courses->count() }} Course{{ $courses->count() !== 1 ? 's' : '' }} in Cart</h2>

                @foreach($courses as $course)
                    <div class="cart-item" data-course-id="{{ $course->id }}">
                        <img
                            src="{{ $course->thumbnail ?? 'https://placehold.co/120x80' }}"
                            alt="{{ $course->title }}"
                            class="cart-item-thumb"
                            onerror="this.src='https://placehold.co/120x80'"
                        >
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">
                                <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                            </h3>
                            <p class="cart-item-instructor">by {{ $course->instructor->name ?? 'KoreSearch Instructor' }}</p>
                            <span class="badge badge-{{ $course->level }}">{{ ucfirst($course->level) }}</span>
                        </div>
                        <div class="cart-item-price">
                            @if(isset($priceChanges[$course->id]))
                                <span class="price old-price">৳{{ number_format($priceChanges[$course->id]['old']) }}</span>
                                <span class="price new-price">৳{{ number_format($priceChanges[$course->id]['new']) }}</span>
                                <span class="price-warning-badge">Price Changed</span>
                            @else
                                @if($course->isFree())
                                    <span class="price free">Free</span>
                                @else
                                    <span class="price">৳{{ number_format($course->price) }}</span>
                                @endif
                            @endif
                        </div>
                        <button class="btn-remove-cart" data-course-id="{{ $course->id }}" title="Remove" aria-label="Remove">✕</button>
                    </div>
                @endforeach
            </div>

            <div class="cart-summary">
                <h3 class="cart-summary-title">Order Summary</h3>
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span>৳{{ number_format($total) }}</span>
                </div>
                <div class="cart-summary-row total">
                    <strong>Total</strong>
                    <strong>৳{{ number_format($total) }}</strong>
                </div>

                @auth
                    @foreach($courses as $course)
                        <a href="{{ route('checkout.index') }}" class="btn btn-accent btn-block mt-md">
                            Proceed to Checkout
                        </a>
                        @break
                    @endforeach
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block mt-md">
                        Login to Checkout
                    </a>
                @endauth
            </div>
        </div>
    @endif
</div>

@endsection
