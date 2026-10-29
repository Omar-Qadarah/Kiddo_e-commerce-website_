
<aside class="admin-sidebar">
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
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/categories.php"
           class="sidebar-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">
            <i class="bi bi-tags"></i>
            <span>Categories</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/age_groups.php"
           class="sidebar-link <?= ($activePage ?? '') === 'age_groups' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Age Groups</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/products.php"
           class="sidebar-link <?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>
            <span>Products</span>
        </a>

        <span class="sidebar-label">MANAGEMENT</span>

        <a href="<?= BASE_URL ?>/admin/users.php"
           class="sidebar-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
            <i class="bi bi-person"></i>
            <span>Users</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/orders.php"
           class="sidebar-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">
            <i class="bi bi-bag"></i>
            <span>Orders</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/discounts.php"
           class="sidebar-link <?= ($activePage ?? '') === 'discounts' ? 'active' : '' ?>">
            <i class="bi bi-percent"></i>
            <span>Discounts</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/reviews.php"
           class="sidebar-link <?= ($activePage ?? '') === 'reviews' ? 'active' : '' ?>">
            <i class="bi bi-star"></i>
            <span>Reviews</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="<?= BASE_URL ?>/index.php" class="sidebar-link">
            <i class="bi bi-house"></i>
            <span>View Website</span>
        </a>
    </div>
</aside>
