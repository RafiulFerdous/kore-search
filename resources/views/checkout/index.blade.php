@extends('layouts.app')

@section('title', 'Checkout — KoreSearch')

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Checkout</h1>
        <p class="page-subtitle">Complete your payment via bKash</p>
    </div>
</div>

<div class="container">
    <div class="checkout-layout">

        <div class="checkout-form-section">
            <div class="bkash-header">
                <div class="bkash-logo">bKash</div>
                <p>Send payment to bKash number: <strong>01XXXXXXXXX</strong></p>
                <p class="bkash-note">After sending payment, enter your transaction number below to confirm your order.</p>
            </div>

            <form method="POST" action="{{ route('checkout.process') }}" class="checkout-form" id="checkoutForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">Select Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->title }} — ৳{{ number_format($course->price) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="transaction_number">bKash Transaction Number</label>
                    <input
                        type="text"
                        name="transaction_number"
                        id="transaction_number"
                        class="form-input @error('transaction_number') is-error @enderror"
                        placeholder="e.g. 8NK2031ABC"
                        value="{{ old('transaction_number') }}"
                        required
                    >
                    @error('transaction_number')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="checkout-total">
                    <span>Total Amount:</span>
                    <strong>৳{{ number_format($total) }}</strong>
                </div>

                <button type="submit" class="btn btn-accent btn-block btn-lg" id="confirmPayBtn">
                    Confirm Payment
                </button>
            </form>
        </div>

        <div class="checkout-summary">
            <h3 class="summary-title">Order Summary</h3>
            @foreach($courses as $course)
                <div class="summary-item">
                    <img
                        src="{{ $course->thumbnail ?? 'https://placehold.co/60x40' }}"
                        alt="{{ $course->title }}"
                        class="summary-thumb"
                        onerror="this.src='https://placehold.co/60x40'"
                    >
                    <div class="summary-item-info">
                        <p class="summary-item-title">{{ $course->title }}</p>
                        <p class="summary-item-price">
                            @if($course->isFree())
                                Free
                            @else
                                ৳{{ number_format($course->price) }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
            <div class="summary-total">
                <span>Total</span>
                <strong>৳{{ number_format($total) }}</strong>
            </div>
        </div>

    </div>
</div>

@endsection
