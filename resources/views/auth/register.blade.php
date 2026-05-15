@extends('layouts.app')

@section('title', 'Register — KoreSearch')

@section('content')

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <a href="{{ route('home') }}" class="auth-brand">
                <span class="brand-icon">K</span> KoreSearch
            </a>
            <h1 class="auth-title">Create Your Account</h1>
            <p class="auth-subtitle">Join thousands of learners on KoreSearch</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-input @error('name') is-error @enderror"
                    value="{{ old('name') }}"
                    placeholder="Your full name"
                    required
                    autofocus
                >
                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-input @error('email') is-error @enderror"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                >
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-input @error('password') is-error @enderror"
                    placeholder="Minimum 8 characters"
                    required
                >
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-input"
                    placeholder="Repeat your password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>

        <p class="auth-footer-text">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>

@endsection
