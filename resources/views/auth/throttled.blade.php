@extends('layouts.app')

@section('title', 'Too Many Attempts — KoreSearch')

@section('content')

<div class="auth-wrapper">
    <div class="auth-card" style="text-align:center;">
        <div class="auth-header">
            <a href="{{ route('home') }}" class="auth-brand">
                <span class="brand-icon">K</span> KoreSearch
            </a>
            <div style="font-size:3rem;margin-bottom:12px;">⏳</div>
            <h1 class="auth-title">Too Many Attempts</h1>
            <p class="auth-subtitle">Please wait before trying again.</p>
        </div>

        <div id="timer" style="font-size:3.5rem;font-weight:800;color:var(--color-primary);font-family:'Sora',sans-serif;margin:24px 0 8px;">
            <span id="seconds">{{ $seconds }}</span><span style="font-size:1.2rem;font-weight:500;">s</span>
        </div>

        <p style="color:var(--color-text-muted);font-size:0.9rem;margin-bottom:28px;">
            You've made too many login attempts. The timer will reset automatically,
            or you can try again once it reaches zero.
        </p>

        <a href="{{ route('login') }}" id="retryBtn" class="btn btn-primary" style="pointer-events:none;opacity:0.5;">
            Back to Login
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var secondsEl = document.getElementById('seconds');
        var retryBtn = document.getElementById('retryBtn');
        var remaining = parseInt(secondsEl.textContent, 10);

        function tick() {
            if (remaining <= 0) {
                secondsEl.textContent = '0';
                retryBtn.textContent = 'Back to Login \u2192';
                retryBtn.style.pointerEvents = 'auto';
                retryBtn.style.opacity = '1';
                return;
            }
            secondsEl.textContent = remaining;
            remaining--;
            setTimeout(tick, 1000);
        }

        tick();
    })();
</script>
@endpush