/* ==========================================================================
   Kiddo's — toy details page script (products/details.php, loaded with "defer")
   1. Gallery: clicking a thumbnail changes the main image
   2. Quantity selector: − / + buttons, never below 1 or above the stock
   Plain JavaScript, no libraries.
   ========================================================================== */

/* ---------- 1. Gallery ---------- */
(function () {
    const gallery = document.querySelector('[data-gallery]');
    if (!gallery) return;

    const mainImage = document.getElementById('pd-main-image');
    const thumbs = gallery.querySelectorAll('.pd-thumb');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (thumb.getAttribute('aria-current') === 'true') return;

            thumbs.forEach(function (t) { t.setAttribute('aria-current', 'false'); });
            thumb.setAttribute('aria-current', 'true');

            // short fade so the change feels smooth
            mainImage.classList.add('is-switching');
            setTimeout(function () {
                mainImage.src = thumb.dataset.src;
                mainImage.alt = thumb.dataset.alt;
                mainImage.classList.remove('is-switching');
            }, 150);
        });
    });
})();

/* ---------- 2. Quantity selector ---------- */
(function () {
    const box = document.querySelector('[data-qty]');
    if (!box) return;

    const input = box.querySelector('.qty-input');
    const minus = box.querySelector('[data-step="-1"]');
    const plus = box.querySelector('[data-step="1"]');
    const min = 1;
    const max = parseInt(input.max, 10) || 1;

    // keep the number a whole number between 1 and the stock
    function setQuantity(value) {
        let qty = parseInt(value, 10);
        if (isNaN(qty) || qty < min) qty = min;
        if (qty > max) qty = max;
        input.value = qty;
        minus.disabled = input.disabled || qty <= min;
        plus.disabled = input.disabled || qty >= max;
    }

    minus.addEventListener('click', function () { setQuantity(parseInt(input.value, 10) - 1); });
    plus.addEventListener('click', function () { setQuantity(parseInt(input.value, 10) + 1); });

    // typed by hand: fix it when the visitor leaves the field
    input.addEventListener('change', function () { setQuantity(input.value); });
    input.addEventListener('blur', function () { setQuantity(input.value); });

    // and once more just before sending the form
    input.form.addEventListener('submit', function () { setQuantity(input.value); });

    setQuantity(input.value);
})();
