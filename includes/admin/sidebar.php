
<aside class="admin-sidebar" id="admin-navigation" aria-label="Admin navigation">
    <button class="admin-nav-close" type="button" aria-label="Close navigation"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
    <div class="sidebar-brand">
        <a href="<?= BASE_URL ?>/admin/index.php">
            <span class="brand-icon">K</span>
            <span>Kiddo's</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <span class="sidebar-label">MAIN MENU</span>

        <a href="<?= BASE_URL ?>/admin/index.php"
           class="sidebar-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/categories.php"
           class="sidebar-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12V4h8l9 9-8 8-9-9zM7.5 7.5h.01"/></svg>
            <span>Categories</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/age_groups.php"
           class="sidebar-link <?= ($activePage ?? '') === 'age_groups' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM4 21a8 8 0 0 1 16 0"/></svg>
            <span>Age Groups</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/products.php"
           class="sidebar-link <?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l8-4 8 4-8 4-8-4zm0 0v10l8 4 8-4V7"/></svg>
            <span>Products</span>
        </a>

        <span class="sidebar-label">MANAGEMENT</span>

        <a href="<?= BASE_URL ?>/admin/users.php"
           class="sidebar-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-5-6"/></svg>
            <span>Users</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/orders.php"
           class="sidebar-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2zM9 7h6M9 11h6M9 15h4"/></svg>
            <span>Orders</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/discounts.php"
           class="sidebar-link <?= ($activePage ?? '') === 'discounts' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 5 5 19M7 7h.01M17 17h.01"/></svg>
            <span>Discounts</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/reviews.php"
           class="sidebar-link <?= ($activePage ?? '') === 'reviews' ? 'active' : '' ?>">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l3 6.5 7 .9-5 4.8 1.3 7L12 18l-6.3 3.2 1.3-7-5-4.8 7-.9z"/></svg>
            <span>Reviews</span>
        </a>
    </nav>

    <?php require_once __DIR__ . '/../csrf.php'; ?>
     
    <form class="sidebar-bottom" action="<?= BASE_URL ?>/auth/logout.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="sidebar-link sidebar-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </button>
    </form>
</aside>
