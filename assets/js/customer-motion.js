// Progressive enhancement: sections are NEVER hidden in CSS or waiting for JS.
(() => {
    const preference = matchMedia('(prefers-reduced-motion: reduce)');
    if (!('IntersectionObserver' in window) || !Element.prototype.animate) return;
    let observer;
    const animations = new Set();
    const seen = new WeakSet();
    function setup() {
        observer?.disconnect();
        animations.forEach(animation => animation.cancel());
        animations.clear();
        if (preference.matches || !document.querySelector('.hero')) return;
        observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting || seen.has(entry.target)) return;
                seen.add(entry.target);
                observer.unobserve(entry.target);
                const animation = entry.target.animate([
                    {opacity:.94, transform:'translateY(10px)'},
                    {opacity:1, transform:'translateY(0)'}
                ], {duration:matchMedia('(max-width:600px)').matches ? 180 : 240,easing:'cubic-bezier(.2,.7,.2,1)'});
                animations.add(animation);
                animation.onfinish = () => animations.delete(animation);
            });
        }, {threshold:.08});
        document.querySelectorAll('main > .shop-section').forEach(section => {
            if (!seen.has(section)) observer.observe(section);
        });
    }
    setup();
    preference.addEventListener('change',setup);
})();
