
(function () {
    // One listener for the whole page ("event delegation"): it works for
    // every card, and each card keeps its own position in data-index.
    document.addEventListener('click', function (e) {
        const arrow = e.target.closest('[data-card-step]');
        if (!arrow) return;

        e.preventDefault();
        e.stopPropagation();

        const media = arrow.closest('[data-card-gallery]');
        let images;
        try {
            images = JSON.parse(media.dataset.images || '[]');
        } catch (err) {
            return;
        }
        if (images.length < 2) return;

        const step = parseInt(arrow.dataset.cardStep, 10);
        const current = parseInt(media.dataset.index || '0', 10);
        // wrap around: after the last image comes the first again
        const next = (current + step + images.length) % images.length;
        media.dataset.index = next;

        const img = media.querySelector('img');
        img.classList.add('is-switching');
        setTimeout(function () {
            img.src = images[next];
            img.classList.remove('is-switching');
        }, 150);

        media.querySelectorAll('.card-dots span').forEach(function (dot, i) {
            dot.classList.toggle('is-on', i === next);
        });
    });
})();

(function () {
    const form = document.querySelector('[data-filter-form]');
    if (!form) return;

    // empty choices ("All ages", "All") are left out of the link: ?gender=boys, not ?age=&gender=boys
    function send() {
        form.querySelectorAll('select, input[type=hidden]').forEach(function (field) {
            field.disabled = field.value === '';
        });
        form.submit();
    }

    form.querySelectorAll('select').forEach(function (select) {
        select.addEventListener('change', send);
    });
    form.addEventListener('submit', function (e) { e.preventDefault(); send(); });

    // coming back with the browser's Back button: turn the fields on again
    window.addEventListener('pageshow', function () {
        form.querySelectorAll('select, input[type=hidden]').forEach(function (field) {
            field.disabled = false;
        });
    });

    const apply = form.querySelector('[data-filter-apply]');
    if (apply) apply.hidden = true;
})();
