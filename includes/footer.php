
<?php
// Age groups for the "Shop by age" links
$footerAges = $pdo->query('SELECT name, slug FROM age_groups ORDER BY sort_order, min_age')->fetchAll();
?></main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/kiddos-logo-white.png" alt="Kiddo's" width="167" height="40">
            </a>
            <p class="footer-tagline">Toys that grow little minds.</p>
            <p>Hand-picked toys for ages 3 to 12, delivered with love across Jordan.</p>
        </div>

        <div>
            <h3>Shop by age</h3>
            <ul>
                <?php foreach ($footerAges as $age): ?>
                    <li><a href="<?= BASE_URL ?>/products/index.php?age=<?= urlencode($age['slug']) ?>"><?= htmlspecialchars($age['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= BASE_URL ?>/products/index.php">All toys</a></li>
            </ul>
        </div>

        <div>
            <h3>Contact us</h3>
            <ul>
                <li><a href="tel:+96260000000">+962 6 000 0000</a></li>
                <li><a href="mailto:care@kiddos.jo">care@kiddos.jo</a></li>
            </ul>
            <div class="footer-social">
                <a href="#" aria-label="Instagram">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5"/></svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="#" aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.5A8.4 8.4 0 1 1 21 11.5z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <h3>Payment</h3>
            <div class="pay-card">
                <span class="pay-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 12h.01M18 12h.01"/></svg>
                </span>
                <span>
                    <strong>Cash on Delivery</strong>
                    <small>Pay when your order arrives</small>
                </span>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <span>&copy; <?= date('Y') ?> Kiddo's. All rights reserved.</span>
        </div>
    </div>
</footer>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['shopping_assistant_csrf'])) {
    $_SESSION['shopping_assistant_csrf'] = bin2hex(random_bytes(32));
}
?>
<div class="shopping-chat" data-endpoint="<?= htmlspecialchars(BASE_URL . '/ai/shopping_assistant.php', ENT_QUOTES, 'UTF-8') ?>" data-csrf="<?= htmlspecialchars($_SESSION['shopping_assistant_csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <section class="shopping-chat__panel" id="shopping-chat-panel" role="dialog" aria-modal="false" aria-labelledby="shopping-chat-title" hidden>
        <header class="shopping-chat__header">
            <span class="shopping-chat__avatar" aria-hidden="true">K</span>
            <div class="shopping-chat__heading">
                <strong id="shopping-chat-title">Kiddo&#8217;s toy helper</strong>
                <span>Here to help you find a favorite</span>
            </div>
            <button class="shopping-chat__close" type="button" aria-label="Close chat">&times;</button>
        </header>
        <div class="shopping-chat__messages" id="shopping-chat-messages" aria-live="polite" aria-relevant="additions text">
            <p class="shopping-chat__bubble shopping-chat__bubble--assistant">Hi! Tell me who you&#8217;re shopping for, or ask me about our toys.</p>
        </div>
        <form class="shopping-chat__form" id="shopping-chat-form">
            <label class="sr-only" for="shopping-chat-input">Your message</label>
            <input id="shopping-chat-input" name="message" type="text" maxlength="1000" autocomplete="off" placeholder="Ask about toys&#8230;" required>
            <button type="submit" aria-label="Send message">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 21 3l-6.5 18-3.2-7.3L3 11.5Zm8.3 2.2L21 3"/></svg>
            </button>
        </form>
        <p class="shopping-chat__note">Please don&#8217;t share personal or payment details.</p>
    </section>
    <button class="shopping-chat__launcher" type="button" aria-label="Open Kiddo&#8217;s shopping assistant" aria-expanded="false" aria-controls="shopping-chat-panel">
        <svg class="shopping-chat__icon" viewBox="0 0 48 48" aria-hidden="true">
            <path d="M24 5.5c-10.2 0-18.5 7.2-18.5 16.1 0 4.8 2.4 9.1 6.3 12.1l-1.2 8.8 9.2-4.1c1.4.3 2.8.5 4.2.5 10.2 0 18.5-7.2 18.5-16.1S34.2 5.5 24 5.5Z" fill="currentColor"/>
            <circle cx="17" cy="22" r="2.2" fill="#fff"/><circle cx="31" cy="22" r="2.2" fill="#fff"/>
            <path d="M17.5 29c1.7 2.1 3.9 3.1 6.5 3.1s4.8-1 6.5-3.1" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/>
            <path d="m35 8 1.2-3.2L39.5 4l-3.3-1.2L35 0l-1.2 2.8L31 4l2.8.8L35 8Z" fill="#ffd166"/>
        </svg>
        <span class="shopping-chat__launcher-label">Ask Kiddo</span>
    </button>
</div>
<script src="<?= BASE_URL ?>/assets/js/shopping-assistant.js?v=<?= filemtime(__DIR__ . '/../assets/js/shopping-assistant.js') ?>" defer></script>

<script src="<?= BASE_URL ?>/assets/js/storefront-ux.js?v=<?= filemtime(__DIR__ . '/../assets/js/storefront-ux.js') ?>" defer></script>
</body>
</html>
