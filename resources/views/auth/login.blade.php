@extends('layouts.app')

@section('title', 'Login — KoreSearch')

@section('content')

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <a href="{{ route('home') }}" class="auth-brand">
                <span class="brand-icon">K</span> KoreSearch
            </a>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to continue learning</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

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
                    autofocus
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
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group form-checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>

        <p class="auth-footer-text">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </p>
    </div>
</div>

@endsection
