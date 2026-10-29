
(function () {
    const slider = document.querySelector('[data-slider]');
    if (!slider) return;

    const track = slider.querySelector('.promo-track');
    const slides = track.children;
    const dotsBox = document.querySelector('[data-slide-dots]');
    const prevBtn = document.querySelector('[data-slide-prev]');
    const nextBtn = document.querySelector('[data-slide-next]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 0;
    let timer = null;

    for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        dot.addEventListener('click', function () { goTo(i); restart(); });
        dotsBox.appendChild(dot);
    }

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        Array.from(dotsBox.children).forEach(function (dot, i) {
            dot.setAttribute('aria-current', i === current ? 'true' : 'false');
        });
        Array.from(slides).forEach(function (slide, i) {
            slide.setAttribute('aria-hidden', i === current ? 'false' : 'true');
            slide.querySelectorAll('a, button').forEach(function (el) {
                el.tabIndex = i === current ? 0 : -1;
            });
        });
    }

    function restart() {
        clearInterval(timer);
        if (!reduceMotion) {
            timer = setInterval(function () { goTo(current + 1); }, 6000);
        }
    }

    prevBtn.addEventListener('click', function () { goTo(current - 1); restart(); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); restart(); });

    // pause while the visitor is looking at a slide
    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', restart);

    // swipe on phones
    let startX = null;
    slider.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', function (e) {
        if (startX === null) return;
        const diff = e.changedTouches[0].clientX - startX;
        if (Math.abs(diff) > 40) {
            goTo(current + (diff < 0 ? 1 : -1));
            restart();
        }
        startX = null;
    });

    goTo(0);
    restart();
})();
