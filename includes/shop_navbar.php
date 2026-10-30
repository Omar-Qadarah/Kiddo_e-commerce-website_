<?php
// Storefront navbar (landing, toys and toy details pages).
// One row: logo on the far left, then Home / Categories / Toys,
// then search, cart and the account area on the right.
// It reuses the classes of assets/css/style.css (brand, header-search,
// icon-link, drawer ...) and adds its own in assets/css/storefront.css.
$activePage = $activePage ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = trim((string) ($_SESSION['user_name'] ?? ''));
$firstName  = preg_split('/\s+/', $userName)[0] ?? '';
$cartCount  = (int) ($_SESSION['cart_count'] ?? 0);
$searchTerm = is_string($_GET['q'] ?? null) ? $_GET['q'] : ''; // text only (a broken URL like ?q[]= is ignored)

// key => [label, path from the project root]
$shopNavLinks = [
    'home'       => ['Home', '/index.php'],
    'categories' => ['Categories', '/products/categories.php'],
    'toys'       => ['Toys', '/products/index.php'],
];
?>
<!-- Keeps the mobile menu open/closed state. Toggled by the labels below, no JS. -->
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
                        <a href="<?= BASE_URL . $path ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>"
                           <?= $activePage === $key ? 'aria-current="page"' : '' ?>><?= $label ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Toy name -> that toy's page, otherwise a "Not found" message (products/find.php) -->
        <form class="header-search" action="<?= BASE_URL ?>/products/find.php" method="get" role="search">
            <label class="sr-only" for="site-search">Search toys by name</label>
            <input id="site-search" type="search" name="q" maxlength="60"
                   placeholder="Search toys by name…"
                   value="<?= htmlspecialchars((string) $searchTerm) ?>">
            <button type="submit" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
            </button>
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
                <span class="shop-welcome desktop-only">Welcome, <strong><?= htmlspecialchars($firstName) ?>!</strong></span>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/orders/index.php">My orders</a>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/auth/login.php">Login</a>
                <a class="btn btn-primary btn-sm desktop-only" href="<?= BASE_URL ?>/auth/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile drawer (same classes as the shared navbar) -->
<label for="nav-toggle" class="drawer-backdrop" aria-hidden="true"></label>

<aside class="drawer" aria-label="Menu">
    <div class="drawer-head">
        <img src="<?= BASE_URL ?>/assets/images/kiddos-logo-white.png" alt="Kiddo's" width="150" height="36">
        <label for="nav-toggle" class="drawer-close" aria-label="Close menu">&times;</label>
    </div>

    <?php if ($isLoggedIn): ?>
        <div class="drawer-auth is-user">
            <span class="shop-welcome">Welcome, <strong><?= htmlspecialchars($firstName) ?>!</strong></span>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/orders/index.php">My orders</a>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
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
