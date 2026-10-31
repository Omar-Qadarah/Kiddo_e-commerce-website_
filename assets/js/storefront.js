 
 
document.addEventListener('DOMContentLoaded', function () {

     
    document.querySelectorAll('[data-new-arrivals-carousel]').forEach(function (carousel) {
        var track = carousel.querySelector('[data-new-arrivals-track]');
        var previous = document.querySelector('[data-new-arrivals-prev]');
        var next = document.querySelector('[data-new-arrivals-next]');
        if (!track || !previous || !next) return;

        function updateControls() {
            var maxScroll = track.scrollWidth - track.clientWidth;
            previous.disabled = track.scrollLeft <= 2;
            next.disabled = maxScroll <= 2 || track.scrollLeft >= maxScroll - 2;
        }

        previous.addEventListener('click', function () {
            track.scrollBy({ left: -track.clientWidth * 0.85, behavior: 'smooth' });
        });
        next.addEventListener('click', function () {
            track.scrollBy({ left: track.clientWidth * 0.85, behavior: 'smooth' });
        });
        track.addEventListener('scroll', updateControls, { passive: true });
        window.addEventListener('resize', updateControls);
        requestAnimationFrame(updateControls);
    });

     
    document.querySelectorAll('[data-card-gallery]').forEach(function (media) {
        var images = media.querySelectorAll('.toy-card-circle img');
        var dots = media.querySelectorAll('.card-dots span');
        var current = 0;

        media.querySelectorAll('[data-card-step]').forEach(function (arrow) {
            arrow.addEventListener('click', function (event) {
                 
                event.preventDefault();
                event.stopPropagation();

                var step = parseInt(arrow.getAttribute('data-card-step'), 10);
                images[current].classList.remove('is-shown');
                dots[current].classList.remove('is-on');

                current = (current + step + images.length) % images.length;

                images[current].classList.add('is-shown');
                dots[current].classList.add('is-on');
            });
        });
    });

     
    document.querySelectorAll('[data-slider]').forEach(function (slider) {
        var slides = slider.querySelectorAll('.promo-slide');
        var dots = slider.querySelectorAll('[data-slide-to]');
         
        var names = slider.querySelectorAll('[data-slide-name]');
        var current = 0;
        var timer = null;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

        function show(index) {
            slides[current].classList.remove('is-active');
            dots[current].classList.remove('is-on');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('is-active');
            dots[current].classList.add('is-on');

             
            names.forEach(function (name, i) {
                name.classList.toggle('is-on', i === current);
                name.setAttribute('aria-selected', i === current ? 'true' : 'false');
            });
        }

        function restart() {
            clearInterval(timer);
            if (reducedMotion.matches || document.hidden) return;
            timer = setInterval(function () { show(current + 1); }, 6000);
        }

        slider.querySelector('[data-slide-prev]').addEventListener('click', function () { show(current - 1); restart(); });
        slider.querySelector('[data-slide-next]').addEventListener('click', function () { show(current + 1); restart(); });
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                show(parseInt(dot.getAttribute('data-slide-to'), 10));
                restart();
            });
        });

        names.forEach(function (name) {
            name.addEventListener('click', function () {
                show(parseInt(name.getAttribute('data-slide-name'), 10));
                restart();
            });
        });

        reducedMotion.addEventListener('change', restart);
        document.addEventListener('visibilitychange', restart);
        restart();
    });

     
    document.querySelectorAll('[data-filter-form] select').forEach(function (select) {
        select.addEventListener('change', function () {
            select.form.submit();
        });
    });

     
    document.querySelectorAll('[data-gallery]').forEach(function (gallery) {
        var mainImage = gallery.querySelector('[data-gallery-main]');
        var thumbs = gallery.querySelectorAll('[data-gallery-thumb]');

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                mainImage.src = thumb.getAttribute('data-gallery-thumb');
                thumbs.forEach(function (other) {
                    other.classList.remove('is-active');
                    other.setAttribute('aria-pressed', 'false');
                });
                thumb.classList.add('is-active');
                thumb.setAttribute('aria-pressed', 'true');
            });
        });

         
        gallery.querySelectorAll('[data-gallery-step]').forEach(function (arrow) {
            arrow.addEventListener('click', function () {
                var current = 0;
                thumbs.forEach(function (thumb, index) {
                    if (thumb.classList.contains('is-active')) {
                        current = index;
                    }
                });

                var step = parseInt(arrow.getAttribute('data-gallery-step'), 10);
                var next = (current + step + thumbs.length) % thumbs.length;
                thumbs[next].click();
            });
        });
    });

     
    document.querySelectorAll('[data-qty]').forEach(function (box) {
        var input = box.querySelector('input');
        var min = parseInt(input.min, 10) || 1;
        var max = parseInt(input.max, 10) || 99;

        function setValue(value) {
            input.value = Math.min(max, Math.max(min, value || min));
        }

        box.querySelector('[data-qty-minus]').addEventListener('click', function () {
            setValue(parseInt(input.value, 10) - 1);
        });
        box.querySelector('[data-qty-plus]').addEventListener('click', function () {
            setValue(parseInt(input.value, 10) + 1);
        });
        input.addEventListener('change', function () {
            setValue(parseInt(input.value, 10));
        });
    });
});

 document.addEventListener('submit', async function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement) || !new URL(form.action).pathname.endsWith('/cart/add.php')) return;
    event.preventDefault();
    if (form.dataset.adding === 'true') return;
    form.dataset.adding = 'true';
    var button = form.querySelector('button[type="submit"], button:not([type])');
    if (button) button.disabled = true;
    function notify(message, success) {
        var toast = document.getElementById('cart-notification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'cart-notification';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);
        }
        clearTimeout(toast.hideTimer);
        toast.textContent = message;
        toast.style.backgroundColor = success ? '#16834a' : '#b42332';
        toast.hidden = false;
        toast.hideTimer = setTimeout(function () { toast.hidden = true; }, 3500);
    }
    try {
        var response = await fetch(form.action, {
            method: 'POST', body: new FormData(form),
            headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'
        });
        if (!response.ok) throw new Error('Unable to add item. Please refresh and try again.');
        var result = await response.json();
        if (result.success) {
            document.querySelectorAll('a[aria-label="Shopping cart"]').forEach(function (link) {
                var badge = link.querySelector('.badge-count');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge-count';
                    link.appendChild(badge);
                }
                badge.textContent = result.count;
            });
        }
        notify(result.message, result.success);
    } catch (error) {
        notify('Unable to add item. Please refresh and try again.', false);
    } finally {
        delete form.dataset.adding;
        if (button) button.disabled = false;
    }
});
