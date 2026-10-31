<?php
 
 
 
 
 
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
$activePage = $activePage ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = trim((string) ($_SESSION['user_name'] ?? ''));
$nameParts  = preg_split('/\s+/u', $userName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
$firstName  = $nameParts[0] ?? '';
$cartCount  = (int) ($_SESSION['cart_count'] ?? 0);
$searchTerm = is_string($_GET['q'] ?? null) ? $_GET['q'] : '';  

 
$shopNavLinks = [
    'home'       => ['Home', '/index.php'],
    'categories' => ['Categories', '/products/categories.php'],
    'toys'       => ['Toys', '/products/index.php'],
    'offers' => ['Offers', '/products/index.php?offers=1'],
];
?>
 
<input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true" tabindex="-1">

<header class="site-header shop-header">
    <div class="container shop-header-row">
        <label for="nav-toggle" class="menu-btn" aria-label="Open menu">
            <span></span><span></span><span></span>
        </label>

        <a class="brand" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/assets/images/kiddos-logo.png" alt="Kiddo's" width="183" height="44">
        </a>

        <nav class="shop-nav" aria-label="Main">
            <ul>
                <?php foreach ($shopNavLinks as $key => [$label, $path]): ?>
                    <li>
                        <a href="<?= BASE_URL . $path ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>><?= $label ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

         
        <form class="header-search" data-suggestions="<?= BASE_URL ?>/products/suggestions.php" action="<?= BASE_URL ?>/products/find.php" method="get" role="search">
            <label class="sr-only" for="site-search">Search toys by name</label>
            <input id="site-search" type="search" autocomplete="off" aria-controls="search-suggestions" aria-expanded="false" name="q" maxlength="60"
                   placeholder="Search toys by name…"
                   value="<?= htmlspecialchars((string) $searchTerm) ?>">
            <button type="submit" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
            </button>
            <div id="search-suggestions" class="search-suggestions" hidden></div>
        </form>

        <div class="header-actions">
            <a class="icon-link" href="<?= BASE_URL ?>/cart/index.php" aria-label="Shopping cart">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.4L21 8H6.2"/>
                    <circle cx="10" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
                </svg>
                <?php if ($cartCount > 0): ?>
                    <span class="badge-count"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <span class="header-divider desktop-only"></span>

            <?php if ($isLoggedIn): ?>
                <?php require __DIR__ . '/account_menu.php'; ?>
            <?php else: ?>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/auth/login.php">Login</a>
                <a class="btn btn-primary btn-sm desktop-only" href="<?= BASE_URL ?>/auth/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

 
<label for="nav-toggle" class="drawer-backdrop" aria-hidden="true"></label>

<aside class="drawer" aria-label="Menu">
    <div class="drawer-head">
        <img src="<?= BASE_URL ?>/assets/images/kiddos-logo-white.png" alt="Kiddo's" width="150" height="36">
        <label for="nav-toggle" class="drawer-close" aria-label="Close menu">&times;</label>
    </div>

    <?php if ($isLoggedIn): ?>
        <div class="drawer-auth is-user">
            <?php if ($activePage === 'profile'): ?>
                <div class="drawer-account-sections" aria-label="Profile sections">
                    <button type="button" class="drawer-account-link account-tab-link <?= ($activeAccountTab ?? '') === 'orders' ? 'is-active' : '' ?>" onclick="switchTab('orders', this); document.getElementById('nav-toggle').checked = false;" aria-controls="tab-orders" <?= ($activeAccountTab ?? '') === 'orders' ? 'aria-current="page"' : '' ?>>🛍️ My Orders</button>
                    <button type="button" class="drawer-account-link account-tab-link <?= ($activeAccountTab ?? '') === 'reviews' ? 'is-active' : '' ?>" onclick="switchTab('reviews', this); document.getElementById('nav-toggle').checked = false;" aria-controls="tab-reviews" <?= ($activeAccountTab ?? '') === 'reviews' ? 'aria-current="page"' : '' ?>>⭐ My Reviews</button>
                    <button type="button" class="drawer-account-link account-tab-link <?= ($activeAccountTab ?? '') === 'profile' ? 'is-active' : '' ?>" onclick="switchTab('profile', this); document.getElementById('nav-toggle').checked = false;" aria-controls="tab-profile" <?= ($activeAccountTab ?? '') === 'profile' ? 'aria-current="page"' : '' ?>>👤 My Profile</button>
                </div>
            <?php else: ?>
                <a class="drawer-account-link" href="<?= BASE_URL ?>/orders/index.php">🛍️ My Orders</a>
                <a class="drawer-account-link" href="<?= BASE_URL ?>/auth/profile.php?tab=reviews">⭐ My Reviews</a>
                <a class="drawer-account-link" href="<?= BASE_URL ?>/auth/profile.php">👤 My Profile</a>
            <?php endif; ?>
            <form action="<?= BASE_URL ?>/auth/logout.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button class="account-menu__logout" type="submit">Log out</button>
            </form>
        </div>
    <?php else: ?>
        <div class="drawer-auth">
            <a class="btn btn-outline" href="<?= BASE_URL ?>/auth/login.php">Login</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/register.php">Register</a>
        </div>
    <?php endif; ?>

    <ul class="drawer-links">
        <?php foreach ($shopNavLinks as $key => [$label, $path]): ?>
            <li>
                <a href="<?= BASE_URL . $path ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>"><?= $label ?> <span aria-hidden="true">›</span></a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
