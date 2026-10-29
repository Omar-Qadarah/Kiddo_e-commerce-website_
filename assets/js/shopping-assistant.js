(() => {
    const widget = document.querySelector('.shopping-chat');
    if (!widget) return;

    const launcher = widget.querySelector('.shopping-chat__launcher');
    const panel = widget.querySelector('.shopping-chat__panel');
    const closeButton = widget.querySelector('.shopping-chat__close');
    const form = widget.querySelector('#shopping-chat-form');
    const input = widget.querySelector('#shopping-chat-input');
    const messages = widget.querySelector('#shopping-chat-messages');
    const endpoint = widget.dataset.endpoint;
    const csrfToken = widget.dataset.csrf;
    const storageKey = 'kiddos-shopping-assistant-v1';
    let history = [];
    try {
        const saved = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
        if (Array.isArray(saved)) history = saved.filter((turn) =>
            turn && ['user', 'model'].includes(turn.role) && typeof turn.text === 'string'
        ).slice(-8);
    } catch (_) {
        history = [];
    }

    const appendMessage = (text, role, status = false) => {
        const bubble = document.createElement('p');
        bubble.className = `shopping-chat__bubble shopping-chat__bubble--${role}`;
        if (status) bubble.classList.add('shopping-chat__bubble--status');
        bubble.textContent = text;
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
        return bubble;
    };

    const appendProductCards = (products) => {
        if (!Array.isArray(products) || !products.length) return;
        const list = document.createElement('div');
        list.className = 'shopping-chat__products';

        products.slice(0, 3).forEach((product) => {
            if (!product || typeof product.name !== 'string' || typeof product.url !== 'string') return;
            let url;
            try {
                url = new URL(product.url, window.location.href);
            } catch (_) {
                return;
            }
            if (url.origin !== window.location.origin) return;

            const card = document.createElement('a');
            card.className = 'shopping-chat__product-card';
            card.href = url.href;
            card.setAttribute('aria-label', `View ${product.name}`);

            const image = document.createElement('img');
            image.className = 'shopping-chat__product-image';
            image.src = typeof product.image === 'string' ? product.image : '';
            image.alt = '';
            image.loading = 'lazy';
            card.appendChild(image);

            const details = document.createElement('span');
            details.className = 'shopping-chat__product-details';
            const name = document.createElement('strong');
            name.className = 'shopping-chat__product-name';
            name.textContent = product.name;
            details.appendChild(name);

            const meta = document.createElement('span');
            meta.className = 'shopping-chat__product-meta';
            meta.textContent = [product.category, product.ageRange].filter(Boolean).join(' · ');
            details.appendChild(meta);

            const footer = document.createElement('span');
            footer.className = 'shopping-chat__product-footer';
            const price = document.createElement('strong');
            price.textContent = typeof product.price === 'string' ? product.price : '';
            const linkLabel = document.createElement('span');
            linkLabel.textContent = 'View toy →';
            footer.append(price, linkLabel);
            details.appendChild(footer);
            card.appendChild(details);
            list.appendChild(card);
        });

        if (list.childElementCount) {
            messages.appendChild(list);
            messages.scrollTop = messages.scrollHeight;
        }
    };

    if (history.length) {
        messages.replaceChildren();
        history.forEach((turn) => {
            appendMessage(turn.text, turn.role === 'user' ? 'user' : 'assistant');
            if (turn.role === 'model') appendProductCards(turn.products);
        });
    }

    const openPanel = () => {
        panel.hidden = false;
        launcher.setAttribute('aria-expanded', 'true');
        window.setTimeout(() => input.focus(), 0);
    };
    const closePanel = () => {
        panel.hidden = true;
        launcher.setAttribute('aria-expanded', 'false');
        launcher.focus();
    };

    launcher.addEventListener('click', () => panel.hidden ? openPanel() : closePanel());
    closeButton.addEventListener('click', closePanel);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) closePanel();
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = input.value.trim();
        if (!message || form.dataset.sending === 'true') return;

        appendMessage(message, 'user');
        input.value = '';
        form.dataset.sending = 'true';
        const sendButton = form.querySelector('button[type="submit"]');
        sendButton.disabled = true;
        const thinking = appendMessage('Thinking...', 'assistant', true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Chat-CSRF': csrfToken
                },
                body: JSON.stringify({
                    message,
                    history: history.slice(-8).map((turn) => ({ role: turn.role, text: turn.text }))
                })
            });
            const result = await response.json();
            thinking.remove();
            if (!response.ok || typeof result.reply !== 'string') {
                throw new Error(result.error || 'Please try again in a moment.');
            }

            appendMessage(result.reply, 'assistant');
            const products = Array.isArray(result.products) ? result.products.slice(0, 3) : [];
            appendProductCards(products);
            history = [...history, { role: 'user', text: message }, { role: 'model', text: result.reply, products }].slice(-8);
            sessionStorage.setItem(storageKey, JSON.stringify(history));
        } catch (error) {
            thinking.remove();
            appendMessage(error.message || 'I could not answer just now. Please try again.', 'assistant');
        } finally {
            form.dataset.sending = 'false';
            sendButton.disabled = false;
            input.focus();
        }
    });
})();
