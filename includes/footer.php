</main>

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
            <h3>Shop</h3>
            <ul>
                <li><a href="<?= BASE_URL ?>/products/index.php">All toys</a></li>
                <li><a href="<?= BASE_URL ?>/products/search.php">Search</a></li>
                <li><a href="<?= BASE_URL ?>/ai/shopping_assistant.php">AI Toy Finder</a></li>
            </ul>
        </div>

        <div>
            <h3>Customer Service</h3>
            <ul>
                <li><a href="<?= BASE_URL ?>/orders/index.php">My Orders</a></li>
                <li><a href="<?= BASE_URL ?>/cart/index.php">Cart</a></li>
                <li><a href="<?= BASE_URL ?>/auth/profile.php">Profile</a></li>
            </ul>
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
            <span>Amman, Jordan</span>
        </div>
    </div>
</footer>

</body>
</html>