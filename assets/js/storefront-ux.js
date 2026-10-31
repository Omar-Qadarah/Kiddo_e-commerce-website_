// Small progressive enhancements; links and forms keep their native behavior.
document.addEventListener('click', event => {
    document.querySelectorAll('details.account-menu[open]').forEach(menu => {
        if (!menu.contains(event.target)) menu.open = false;
    });
});
document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('details.account-menu[open]').forEach(menu => {
        menu.open = false;
        menu.querySelector('summary')?.focus();
    });
});
document.querySelectorAll('.age-grid, .agecat-grid').forEach(track => {
    const wrapper = document.createElement('div');
    wrapper.className = 'age-carousel';
    track.before(wrapper);
    wrapper.append(track);
    track.classList.add('age-carousel-track');
    const buttons = [-1, 1].map(direction => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'age-carousel-arrow';
        button.setAttribute('aria-label', direction < 0 ? 'Previous age groups' : 'Next age groups');
        button.textContent = direction < 0 ? '←' : '→';
        direction < 0 ? wrapper.prepend(button) : wrapper.append(button);
        button.addEventListener('click', () => {
            const card = track.firstElementChild;
            const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            track.scrollBy({left: direction * (card.getBoundingClientRect().width + gap),
                behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'});
        });
        return button;
    });
    const update = () => {
        const visible = Math.min(5, track.children.length || 1, Math.max(1, Math.floor(track.clientWidth / 175)));
        track.style.setProperty('--age-visible', visible);
        const end = track.scrollWidth - track.clientWidth;
        buttons[0].disabled = track.scrollLeft <= 2;
        buttons[1].disabled = track.scrollLeft >= end - 2;
        buttons.forEach(button => {button.hidden = end <= 2;});
    };
    track.addEventListener('scroll', update, {passive: true});
    new ResizeObserver(update).observe(track);
    update();
});

document.addEventListener('click', event => {
    document.querySelectorAll('[data-navigation-menu][open]').forEach(menu => {
        if (!menu.contains(event.target)) menu.open = false;
    });
});
document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('[data-navigation-menu][open]').forEach(menu => {
        menu.open = false;
        menu.querySelector('summary')?.focus();
    });
});
document.querySelectorAll('[data-filter-form] input[type="checkbox"]').forEach(input => {
    input.addEventListener('change', () => input.form.requestSubmit());
});

// Mobile filtering stays available without putting a long form before every product.
document.querySelectorAll('.browse-filter-panel').forEach(panel => {
    const mobile = matchMedia('(max-width: 600px)');
    const sync = () => {panel.open = !mobile.matches;};
    sync();
    mobile.addEventListener('change', sync);
});
