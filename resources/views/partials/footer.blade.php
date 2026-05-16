<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-col footer-col-brand">
                <div class="footer-brand">
                    <span class="brand-icon">K</span>
                    <span>KoreSearch</span>
                </div>
                <p class="footer-desc">Empowering learners worldwide with expert-led courses in development, design, and technology. Build the skills your career deserves.</p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2zM4 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="GitHub">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Quick Links</h4>
                <nav class="footer-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('courses.index') }}">Browse Courses</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                </nav>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Account</h4>
                <nav class="footer-nav">
                    @auth
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form-footer').submit();">Logout</a>
                        <form id="logout-form-footer" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </nav>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Contact</h4>
                <nav class="footer-nav footer-nav-contact">
                    <a href="mailto:support@koresearch.com">support@koresearch.com</a>
                    <span class="footer-contact-item">Dhaka, Bangladesh</span>
                </nav>
                <div class="footer-newsletter">
                    <p class="footer-newsletter-label">Stay updated with new courses.</p>
                    <div class="footer-newsletter-form">
                        <input type="email" placeholder="Your email" class="footer-newsletter-input" aria-label="Email for newsletter">
                        <button class="footer-newsletter-btn" aria-label="Subscribe">&rarr;</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">&copy; {{ date('Y') }} KoreSearch. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="{{ asset('js/application.js') }}"></script>