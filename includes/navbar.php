<?php
$activePage = $activePage ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? '';
$cartCount  = (int) ($_SESSION['cart_count'] ?? 0);
$searchTerm = $_GET['q'] ?? '';

// "Hind Bundoq" -> "HB", "هند بندق" -> "هب"
// mb_* functions count whole characters, so Arabic letters aren't cut in half.
$nameParts = preg_split('/\s+/', trim($userName), -1, PREG_SPLIT_NO_EMPTY);
$firstName = $nameParts[0] ?? '';
$initials  = '';
foreach (array_slice($nameParts, 0, 2) as $part) {
    $initials .= mb_strtoupper(mb_substr($part, 0, 1));
}

// Main menu. key => [label, path from the project root]
$navLinks = [
    'home' => ['Home', '/index.php'],
    'toys' => ['Toys', '/products/index.php'],
];
if ($isLoggedIn) {
    $navLinks['orders'] = ['My Orders', '/orders/index.php'];
}
?>
<div class="announce">
    🎂 Is it your child's birthday? Get <strong>10% off</strong> at checkout!
</div>

<!-- Keeps the mobile menu open/closed state. Toggled by the labels below, no JS. -->
<input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true" tabindex="-1">

<header class="site-header">
    <div class="container header-main">
        <label for="nav-toggle" class="menu-btn" aria-label="Open menu">
            <span></span><span></span><span></span>
        </label>

        <a class="brand" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/assets/images/kiddos-logo.png" alt="Kiddo's" width="183" height="44">
        </a>

        <form class="header-search" action="<?= BASE_URL ?>/products/search.php" method="get" role="search">
            <label class="sr-only" for="site-search">Search toys by name</label>
            <input id="site-search" type="search" name="q" maxlength="60"
                   placeholder="Search toys by name…"
                   value="<?= htmlspecialchars($searchTerm) ?>">
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
                <a class="user-chip desktop-only" href="<?= BASE_URL ?>/auth/profile.php">
                    <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                    Hi, <?= htmlspecialchars($firstName) ?>!
                </a>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline btn-sm desktop-only" href="<?= BASE_URL ?>/auth/login.php">Login</a>
                <a class="btn btn-primary btn-sm desktop-only" href="<?= BASE_URL ?>/auth/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="header-nav" aria-label="Main">
        <ul class="container">
            <?php foreach ($navLinks as $key => [$label, $path]): ?>
                <li>
                    <a href="<?= BASE_URL . $path ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>"><?= $label ?></a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="<?= BASE_URL ?>/ai/shopping_assistant.php" class="ai-pill <?= $activePage === 'ai' ? 'is-active' : '' ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2 6.5L20.5 10 14 12l-2 6.5L10 12 3.5 10 10 8.5z"/></svg>
                    AI Toy Finder <span class="new-tag">NEW</span>
                </a>
            </li>
        </ul>
    </nav>
</header>

<!-- Mobile drawer -->
<label for="nav-toggle" class="drawer-backdrop" aria-hidden="true"></label>

<aside class="drawer" aria-label="Menu">
    <div class="drawer-head">
        <img src="<?= BASE_URL ?>/assets/images/kiddos-logo-white.png" alt="Kiddo's" width="150" height="36">
        <label for="nav-toggle" class="drawer-close" aria-label="Close menu">&times;</label>
    </div>

    <?php if ($isLoggedIn): ?>
        <div class="drawer-auth is-user">
            <span class="user-chip">
                <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                Hi, <?= htmlspecialchars($firstName) ?>!
            </span>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
        </div>
    <?php else: ?>
        <div class="drawer-auth">
            <a class="btn btn-outline" href="<?= BASE_URL ?>/auth/login.php">Login</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/register.php">Register</a>
        </div>
    <?php endif; ?>

    <ul class="drawer-links">
        <?php foreach ($navLinks as $key => [$label, $path]): ?>
            <li>
                <a href="<?= BASE_URL . $path ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>"><?= $label ?> <span aria-hidden="true">›</span></a>
            </li>
        <?php endforeach; ?>
        <li>
            <a href="<?= BASE_URL ?>/ai/shopping_assistant.php" class="<?= $activePage === 'ai' ? 'is-active' : '' ?>">AI Toy Finder <span aria-hidden="true">›</span></a>
        </li>
    </ul>
</aside>