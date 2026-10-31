 
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
    track.scrollLeft = 0;  
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

 
document.querySelectorAll('.browse-filter-panel').forEach(panel => {
    const mobile = matchMedia('(max-width: 600px)');
    const sync = () => {panel.open = !mobile.matches;};
    sync();
    mobile.addEventListener('change', sync);
});

 
document.querySelectorAll('[data-suggestions]').forEach(form => {
    const input = form.querySelector('input[type="search"]');
    const list = form.querySelector('.search-suggestions');
    let timer, controller;
    const close = () => {list.hidden = true; input.setAttribute('aria-expanded','false');};
    input.addEventListener('input', () => {
        clearTimeout(timer); controller?.abort(); close();
        const query = input.value.trim(); if(query.length < 2) return;
        timer = setTimeout(async () => {
            controller = new AbortController();
            try {
                const response = await fetch(form.dataset.suggestions+'?q='+encodeURIComponent(query),{signal:controller.signal});
                if(!response.ok) return;
                const items = await response.json();
                if(input.value.trim() !== query || !Array.isArray(items)) return;
                list.replaceChildren();
                items.forEach(item => {const link=document.createElement('a');link.href=item.url;link.textContent=item.name;list.append(link);});
                list.hidden = items.length === 0; input.setAttribute('aria-expanded',String(!list.hidden));
            } catch(error) { close(); }
        },250);
    });
    form.addEventListener('keydown',event=>{if(event.key==='Escape')close();if(event.key==='ArrowDown'&&!list.hidden){event.preventDefault();list.querySelector('a')?.focus();}});
    document.addEventListener('click',event=>{if(!form.contains(event.target))close();});
});

 
(() => {
 const state=document.getElementById('nav-toggle');
 const open=document.querySelector('label.menu-btn');
 const close=document.querySelector('label.drawer-close');
 const drawer=document.querySelector('aside.drawer');
 if(!state||!open||!close||!drawer)return;
 [open,close].forEach(control=>{
  control.tabIndex=0;control.setAttribute('role','button');
  control.addEventListener('keydown',event=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();control.click();}});
 });
 open.setAttribute('aria-controls','customer-navigation');drawer.id='customer-navigation';
 const sync=()=>open.setAttribute('aria-expanded',String(state.checked));
 state.addEventListener('change',sync);sync();
 drawer.addEventListener('click',event=>{if(event.target.closest('a[href]')){state.checked=false;sync();}});
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&state.checked){state.checked=false;sync();open.focus();}});
 matchMedia('(min-width:1101px)').addEventListener('change',event=>{if(event.matches){state.checked=false;sync();}});
})();
