<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span class="brand-icon">K</span>
            <span>KoreSearch</span>
        </div>
        <p class="footer-copy">© {{ date('Y') }} koresearch.com — All rights reserved.</p>
        <nav class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('courses.index') }}">Courses</a>
            <a href="{{ route('login') }}">Login</a>
        </nav>
    </div>
</footer>

<script src="{{ asset('js/application.js') }}"></script>
