document.addEventListener('DOMContentLoaded', function () {

    var navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    var hamburgerBtn = document.getElementById('hamburgerBtn');
    var navLinks = document.getElementById('navLinks');
    if (hamburgerBtn && navLinks) {
        hamburgerBtn.addEventListener('click', function () {
            navLinks.classList.toggle('open');
        });
    }

    var userAvatarBtn = document.getElementById('userAvatarBtn');
    var userDropdown = document.getElementById('userDropdown');
    if (userAvatarBtn && userDropdown) {
        userAvatarBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });
        document.addEventListener('click', function () {
            userDropdown.classList.remove('open');
        });
    }

    var flashAlert = document.getElementById('flashAlert');
    if (flashAlert) {
        setTimeout(function () {
            flashAlert.classList.add('fade-out');
            setTimeout(function () {
                if (flashAlert.parentNode) {
                    flashAlert.parentNode.removeChild(flashAlert);
                }
            }, 400);
        }, 4000);
    }

    var addCartButtons = document.querySelectorAll('.btn-add-cart');
    addCartButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var courseId = btn.getAttribute('data-course-id');
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var token = csrfMeta ? csrfMeta.getAttribute('content') : '';

            btn.disabled = true;
            btn.textContent = 'Adding...';

            fetch('/cart/add/' + courseId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ course_id: courseId }),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    btn.textContent = 'Added to Cart ✓';
                    btn.style.background = '#2E7D32';
                    var cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.textContent = data.count;
                    }
                } else {
                    btn.textContent = 'Add to Cart';
                    btn.disabled = false;
                }
            })
            .catch(function () {
                btn.textContent = 'Add to Cart';
                btn.disabled = false;
            });
        });
    });

    var checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            var txnInput = document.getElementById('transaction_number');
            if (!txnInput || txnInput.value.trim() === '') {
                e.preventDefault();
                txnInput.focus();
                txnInput.style.borderColor = '#C62828';
                var existing = txnInput.parentNode.querySelector('.inline-error');
                if (!existing) {
                    var err = document.createElement('span');
                    err.className = 'field-error inline-error';
                    err.textContent = 'Please enter your bKash transaction number.';
                    txnInput.parentNode.appendChild(err);
                }
            }
        });
    }

    var thumbnailInput = document.getElementById('thumbnail');
    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function () {
            var file = thumbnailInput.files[0];
            if (!file) return;

            var allowedTypes = ['image/jpeg', 'image/png'];
            var maxSize = 2 * 1024 * 1024;

            var existingError = thumbnailInput.parentNode.querySelector('.thumb-error');
            if (existingError) existingError.parentNode.removeChild(existingError);

            if (!allowedTypes.includes(file.type)) {
                var err = document.createElement('span');
                err.className = 'field-error thumb-error';
                err.textContent = 'Only JPG and PNG images are allowed.';
                thumbnailInput.parentNode.appendChild(err);
                thumbnailInput.value = '';
                return;
            }

            if (file.size > maxSize) {
                var err2 = document.createElement('span');
                err2.className = 'field-error thumb-error';
                err2.textContent = 'Image must be smaller than 2MB.';
                thumbnailInput.parentNode.appendChild(err2);
                thumbnailInput.value = '';
                return;
            }
        });
    }

    var descTextarea = document.getElementById('description');
    var descCounter = document.getElementById('descCounter');
    if (descTextarea && descCounter) {
        var maxLength = parseInt(descTextarea.getAttribute('maxlength'), 10);

        descTextarea.addEventListener('input', function () {
            var remaining = maxLength - descTextarea.value.length;
            descCounter.textContent = remaining + ' characters remaining';
            if (remaining < 50) {
                descCounter.style.color = '#C62828';
            } else {
                descCounter.style.color = '';
            }
        });
    }

    var dashNavLinks = document.querySelectorAll('.dash-nav-link');
    var dashSections = document.querySelectorAll('.dash-section');

    dashNavLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = link.getAttribute('data-section');

            dashNavLinks.forEach(function (l) { l.classList.remove('active'); });
            link.classList.add('active');

            dashSections.forEach(function (section) {
                if (section.id === targetId) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });
        });
    });

    var filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            var form = select.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

});
