(function () {
    'use strict';

    function showToast(message, type) {
        type = type || 'info';

        var container = document.getElementById('toastContainer');
        if (!container) return;

        var icons = {
            success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>',
            error:   '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
            warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info:    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
        };

        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML =
            '<div class="toast-icon">' + (icons[type] || icons.info) + '</div>' +
            '<span class="toast-message">' + message + '</span>' +
            '<button class="toast-close" aria-label="Close">&times;</button>';

        container.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('toast-visible');
        });

        var timer = setTimeout(function () {
            dismiss(toast);
        }, 4000);

        toast.querySelector('.toast-close').addEventListener('click', function () {
            clearTimeout(timer);
            dismiss(toast);
        });
    }

    function dismiss(toast) {
        if (toast.classList.contains('toast-leaving')) return;
        toast.classList.remove('toast-visible');
        toast.classList.add('toast-leaving');
        setTimeout(function () { toast.remove(); }, 300);
    }

    var container = document.getElementById('toastContainer');
    if (container && container.dataset.flash) {
        try {
            var flash = JSON.parse(container.dataset.flash);
            Object.keys(flash).forEach(function (type) {
                if (flash[type]) showToast(flash[type], type);
            });
            container.removeAttribute('data-flash');
        } catch (e) {}
    }

    var cartBadge = document.getElementById('cartCount');

    function updateBadge(count) {
        if (cartBadge) cartBadge.textContent = count;
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function cartRequest(url, method, btn, done) {
        var token = csrfToken();
        if (!token) return;

        btn.disabled = true;

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(function (r) {
            if (!r.ok) throw new Error('Request failed');
            return r.json();
        })
        .then(function (data) {
            if (data.success) {
                updateBadge(data.count);
                showToast(data.message, 'success');
                if (typeof done === 'function') done(data);
            } else {
                showToast(data.message || 'Could not complete request.', 'error');
            }
        })
        .catch(function () {
            showToast('Something went wrong.', 'error');
        })
        .finally(function () {
            btn.disabled = false;
        });
    }

    document.addEventListener('click', function (e) {
        var addBtn = e.target.closest('.btn-add-cart');
        if (addBtn) {
            e.preventDefault();
            var courseId = addBtn.getAttribute('data-course-id');
            if (!courseId) return;
            var origText = addBtn.textContent;
            addBtn.textContent = 'Adding\u2026';
            cartRequest('/cart/add/' + courseId, 'POST', addBtn, function () {
                addBtn.textContent = origText;
            });
            return;
        }

        var rmBtn = e.target.closest('.btn-remove-cart');
        if (rmBtn) {
            e.preventDefault();
            var courseId = rmBtn.getAttribute('data-course-id');
            if (!courseId) return;

            var item = rmBtn.closest('.cart-item');
            var itemId = item ? item.getAttribute('data-course-id') : null;

            cartRequest('/cart/remove/' + courseId, 'DELETE', rmBtn, function (data) {
                if (item && item.getAttribute('data-course-id') === courseId) {
                    item.style.transition = 'opacity 0.3s ease';
                    item.style.opacity = '0';
                    setTimeout(function () {
                        if (item.parentNode) item.remove();
                        updateCartSummary();
                    }, 300);
                }
                if (data.count === 0) {
                    updateBadge(0);
                }
            });
            return;
        }
    });

    function updateCartSummary() {
        var items = document.querySelectorAll('.cart-item');
        var heading = document.querySelector('.cart-heading');
        var layout = document.querySelector('.cart-layout');

        if (items.length === 0 && layout) {
            layout.innerHTML =
                '<div class="empty-cart">' +
                '<div class="empty-cart-icon">\uD83D\uDED2</div>' +
                '<h2>Your cart is empty</h2>' +
                '<p>Browse our courses and add something you\'d like to learn.</p>' +
                '<a href="/courses" class="btn btn-primary">Browse Courses</a>' +
                '</div>';
            updateBadge(0);
            return;
        }

        if (heading) {
            heading.textContent = items.length + ' Course' + (items.length !== 1 ? 's' : '') + ' in Cart';
        }

        var total = 0;
        items.forEach(function (item) {
            var priceEl = item.querySelector('.cart-item-price .new-price') ||
                          item.querySelector('.cart-item-price .price:not(.old-price):not(.free)');
            if (!priceEl) return;
            var raw = priceEl.textContent.trim().replace(/[^0-9.]/g, '');
            var val = parseFloat(raw);
            if (!isNaN(val)) total += val;
        });

        var formatted = '\u09F3' + Math.round(total).toLocaleString('en-US');
        var summarySpans = document.querySelectorAll('.cart-summary-row span:last-child, .cart-summary-row strong:last-child');
        summarySpans.forEach(function (el) { el.textContent = formatted; });
    }
})();
