document.addEventListener('change', function (event) {
    var input = event.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || !input.accept.includes('image')) return;
    var limit = input.dataset.maxBytes ? Number(input.dataset.maxBytes) : (input.name === 'image' && location.pathname.includes('age_group') ? 2 : 3) * 1024 * 1024;
    input.setCustomValidity('');
    for (var file of input.files) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > limit) {
            input.setCustomValidity('Choose JPG, PNG or WebP images up to ' + (limit / 1024 / 1024) + ' MB each.');
            input.reportValidity();
            break;
        }
    }
}, true);

document.addEventListener('error', function (event) {
    var image = event.target;
    if (!(image instanceof HTMLImageElement) || image.dataset.fallbackApplied === 'yes') return;
    var script = document.querySelector('script[src*="/assets/js/image-validation.js"]');
    if (!script) return;
    image.dataset.fallbackApplied = 'yes';
    image.src = script.src.split('/assets/js/')[0] + '/assets/images/toy-placeholder.svg';
}, true);
