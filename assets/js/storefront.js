// Kiddo's storefront scripts (landing, toys and toy details pages).
// Every part only runs when its HTML exists on the page.
document.addEventListener('DOMContentLoaded', function () {

    // ---------- Toy card arrows: browse a toy's pictures without opening it ----------
    document.querySelectorAll('[data-card-gallery]').forEach(function (media) {
        var images = media.querySelectorAll('.toy-card-circle img');
        var dots = media.querySelectorAll('.card-dots span');
        var current = 0;

        media.querySelectorAll('[data-card-step]').forEach(function (arrow) {
            arrow.addEventListener('click', function (event) {
                // the card itself is a link, so stop the click from opening it
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

    // ---------- Landing page promo slider ----------
    document.querySelectorAll('[data-slider]').forEach(function (slider) {
        var slides = slider.querySelectorAll('.promo-slide');
        var dots = slider.querySelectorAll('[data-slide-to]');
        // names above the slider (each card has its own background colour)
        var names = slider.querySelectorAll('[data-slide-name]');
        var current = 0;
        var timer = null;

        function show(index) {
            slides[current].classList.remove('is-active');
            dots[current].classList.remove('is-on');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('is-active');
            dots[current].classList.add('is-on');

            // the active name follows the card (also when it rotates by itself)
            names.forEach(function (name, i) {
                name.classList.toggle('is-on', i === current);
                name.setAttribute('aria-selected', i === current ? 'true' : 'false');
            });
        }

        function restart() {
            clearInterval(timer);
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

        restart();
    });

    // ---------- Toys page: apply a filter as soon as it changes ----------
    document.querySelectorAll('[data-filter-form] select').forEach(function (select) {
        select.addEventListener('change', function () {
            select.form.submit();
        });
    });

    // ---------- Toy details: thumbnails switch the big picture ----------
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
    });

    // ---------- Toy details: quantity − / + ----------
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
