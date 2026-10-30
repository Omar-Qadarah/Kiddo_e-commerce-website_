(() => {
    /* ========================================================
     * 1. Shopping Assistant Chat Widget (Existing Feature)
     * ======================================================== */
    const widget = document.querySelector('.shopping-chat');
    if (widget) {
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
            if (Array.isArray(saved)) {
                history = saved.filter((turn) =>
                    turn && ['user', 'model'].includes(turn.role) && typeof turn.text === 'string'
                ).slice(-8);
            }
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
                const result = await response.json().catch(() => ({}));
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
    }

    /* ========================================================
     * 2. Personalized Upsell & Cross-sell Injector
     * ======================================================== */
    const recBoxes = document.querySelectorAll('[data-assistant-box]');
    if (!recBoxes.length) return;

    const appBaseUrl = window.BASE_URL || '';
    const csrfValue = window.CSRF_TOKEN || '';

    recBoxes.forEach((container) => {
        const context = container.dataset.context || 'catalog';
        const limit = container.dataset.limit || '4';
        const productIds = container.dataset.productIds || '';

        const apiUrl = `${appBaseUrl}/ai/shopping_assistant.php?action=recommendations&context=${encodeURIComponent(context)}&limit=${encodeURIComponent(limit)}&product_ids=${encodeURIComponent(productIds)}`;

        fetch(apiUrl)
            .then((res) => res.json())
            .then((data) => {
                if (!data.success || !Array.isArray(data.items) || !data.items.length) {
                    container.style.display = 'none';
                    return;
                }

                let outputHtml = '';

                if (context === 'checkout') {
                    const item = data.items[0];
                    outputHtml = `
                        <div class="checkout-addon-bump" style="background:#fffdfa; border:1.5px dashed #f39c12; border-radius:14px; padding:16px; margin:20px 0;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:14px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:54px; height:54px; background:#ffffff; border-radius:10px; border:1px solid #f1f2f6; display:flex; align-items:center; justify-content:center; padding:4px;">
                                        <img src="${item.image}" alt="${item.name}" style="max-width:100%; max-height:100%; object-fit:contain;">
                                    </div>
                                    <div>
                                        <span style="font-size:0.72rem; font-weight:800; color:#d35400; text-transform:uppercase; letter-spacing:0.5px;">Recommended Add-on</span>
                                        <h4 style="font-size:0.95rem; font-weight:700; margin:2px 0; color:#2d3436;">${item.name}</h4>
                                        <strong style="color:#2d3436; font-size:0.95rem;">+${item.priceFormatted}</strong>
                                    </div>
                                </div>
                                <form method="POST" action="${appBaseUrl}/cart/add.php" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="${csrfValue}">
                                    <input type="hidden" name="product_id" value="${item.id}">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="redirect" value="${window.location.pathname}">
                                    <button type="submit" class="btn btn-sm btn-dark" style="border-radius:10px; padding:8px 16px; font-weight:700; cursor:pointer;">
                                        Add to Order
                                    </button>
                                </form>
                            </div>
                        </div>`;
                } else if (context === 'cart') {
                    outputHtml = `
                        <div style="margin-top:36px; padding-top:24px; border-top:1px solid #edf0f5;">
                            <div style="margin-bottom:18px;">
                                <h3 style="font-size:1.3rem; font-weight:800; color:#2d3436; margin-bottom:4px;">Pairs Well With Your Cart</h3>
                                <p style="color:#636e72; font-size:0.9rem; margin:0;">Frequently paired additions for your order</p>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:16px;">`;

                    data.items.forEach((item) => {
                        outputHtml += `
                            <div style="background:#ffffff; border:1px solid #edf0f5; border-radius:14px; padding:14px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
                                <div>
                                    <a href="${item.url}" style="text-decoration:none;">
                                        <div style="height:140px; display:flex; align-items:center; justify-content:center; background:#fafbfc; border-radius:10px; margin-bottom:10px; padding:8px; border:1px solid #f8f9fa;">
                                            <img src="${item.image}" alt="${item.name}" style="max-height:100%; max-width:100%; object-fit:contain;">
                                        </div>
                                        <span style="font-size:0.72rem; color:#6c5ce7; font-weight:700; background:#ede9fe; padding:2px 8px; border-radius:8px;">${item.category}</span>
                                        <h5 style="font-size:0.9rem; font-weight:700; color:#2d3436; margin:6px 0 8px 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${item.name}">${item.name}</h5>
                                    </a>
                                </div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:8px; padding-top:8px; border-top:1px solid #f8f9fa;">
                                    <strong style="font-size:0.95rem; color:#2d3436;">${item.priceFormatted}</strong>
                                    <form method="POST" action="${appBaseUrl}/cart/add.php" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="${csrfValue}">
                                        <input type="hidden" name="product_id" value="${item.id}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius:8px; padding:4px 12px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                                            + Add
                                        </button>
                                    </form>
                                </div>
                            </div>`;
                    });

                    outputHtml += `</div></div>`;
                } else {
                    outputHtml = `
                        <div style="margin:24px 0 36px;">
                            <div style="margin-bottom:16px;">
                                <h3 style="font-size:1.3rem; font-weight:800; color:#2d3436; margin-bottom:4px;">Recommended For You</h3>
                                <p style="color:#636e72; font-size:0.9rem; margin:0;">Picks based on your interests and store favorites</p>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:16px;">`;

                    data.items.forEach((item) => {
                        outputHtml += `
                            <div style="background:#ffffff; border:1px solid #edf0f5; border-radius:14px; padding:14px; display:flex; flex-direction:column; justify-content:space-between;">
                                <div>
                                    <a href="${item.url}" style="text-decoration:none;">
                                        <div style="height:140px; display:flex; align-items:center; justify-content:center; background:#fafbfc; border-radius:10px; margin-bottom:10px; padding:8px;">
                                            <img src="${item.image}" alt="${item.name}" style="max-height:100%; max-width:100%; object-fit:contain;">
                                        </div>
                                        <span style="font-size:0.72rem; color:#6c5ce7; font-weight:700;">${item.category}</span>
                                        <h5 style="font-size:0.9rem; font-weight:700; color:#2d3436; margin:4px 0 8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.name}</h5>
                                    </a>
                                </div>
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:6px;">
                                    <strong style="font-size:0.95rem; color:#2d3436;">${item.priceFormatted}</strong>
                                    <a href="${item.url}" class="btn btn-sm btn-light" style="border-radius:8px; padding:4px 10px; font-size:0.8rem; font-weight:700; text-decoration:none;">View</a>
                                </div>
                            </div>`;
                    });

                    outputHtml += `</div></div>`;
                }

                container.innerHTML = outputHtml;
            })
            .catch(() => {
                container.style.display = 'none';
            });
    });
})();
