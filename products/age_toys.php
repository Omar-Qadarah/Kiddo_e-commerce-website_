<?php
// Age toys page: products/age_toys.php?age=<age card>[&category=<category slug>]
// Opened from an age card on products/categories.php.
// Shows ONLY the toys of the chosen age (0–3, 4–6, 7–10, 10–12 or 12+),
// and of the chosen toy type when the Categories page had one selected.
//
// Reuses the existing parts as they are:
//   includes/age_cards.php (the same age cards as the Categories page),
//   ToyCatalog::getFilteredToys() (same "age ranges overlap" rule as the toys page),
//   includes/toy_card.php (toy cards), toys-page.css + categories-page.css (look).
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../includes/age_cards.php';

$catalog    = new ToyCatalog($pdo);
$categories = $catalog->getCategoriesWithToyCount();

// ---------- Read the URL (only values that really exist are used) ----------
$ageKey = (string) ($_GET['age'] ?? '');
if (!isset($ageCards[$ageKey])) {
    // Unknown or missing age -> back to the age cards
    header('Location: ' . BASE_URL . '/products/categories.php');
    exit;
}
$card = $ageCards[$ageKey];

$activeCategory = null;
$categorySlug   = (string) ($_GET['category'] ?? '');
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory = $category;
    }
}
$categorySlug = $activeCategory ? $activeCategory['slug'] : '';

// ---------- Toys of this age (and type) ----------
$toys = $catalog->getFilteredToys([
    'category' => $categorySlug,
    'age'      => ['min_age' => $card['min'], 'max_age' => $card['max']],
]);
$extraImages = $toys ? $catalog->getImagePathsForToys(array_column($toys, 'id')) : [];

// ---------- Heading ----------
$typeName     = $activeCategory ? $activeCategory['name'] : 'All toys';
$ageLabel     = ageCardRange($card) . ' years';
$categoriesUrl = BASE_URL . '/products/categories.php' . ($categorySlug !== '' ? '?' . http_build_query(['category' => $categorySlug]) : '');

$pageTitle  = $typeName . ' · ' . $ageLabel;
$activePage = 'categories';
$pageStyles = [
    'toys-page.css',
    'categories-page.css',
];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<div class="toys-main">
    <!-- ============ Heading ============ -->
    <section class="toys-hero">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/index.php">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?= BASE_URL ?>/products/categories.php">Categories</a>
            <?php if ($activeCategory): ?>
                <span aria-hidden="true">/</span>
                <a href="<?= htmlspecialchars($categoriesUrl) ?>"><?= htmlspecialchars($typeName) ?></a>
            <?php endif; ?>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?= $ageLabel ?></span>
        </nav>
        <h1><?= $card['emoji'] ?> <?= htmlspecialchars($card['title']) ?> <small>(<?= $ageLabel ?>)</small></h1>
        <p><?= htmlspecialchars($card['text']) ?></p>
    </section>

    <div class="toys-content">
        <!-- ============ Other ages (same toy type) ============ -->
        <nav class="agecat-switch" aria-label="Other ages">
            <?php foreach ($ageCards as $key => $other): ?>
                <a class="agecat-chip agecat-<?= $other['color'] ?> <?= $key === $ageKey ? 'is-active' : '' ?>"
                   href="<?= htmlspecialchars(ageToysUrl($key, $categorySlug)) ?>"
                   <?= $key === $ageKey ? 'aria-current="page"' : '' ?>><?= ageCardRange($other) ?> years</a>
            <?php endforeach; ?>
        </nav>

        <div class="agecat-results-head">
            <p class="result-count" role="status">
                <?= count($toys) ?> <?= count($toys) === 1 ? 'toy' : 'toys' ?>
                <?= $activeCategory ? 'in ' . htmlspecialchars($typeName) . ' ' : '' ?>for ages <?= $ageLabel ?>
            </p>
            <a class="btn btn-outline btn-sm" href="<?= htmlspecialchars($categoriesUrl) ?>">← Back to categories</a>
        </div>

        <!-- ============ Toy grid ============ -->
        <?php if (!empty($toys)): ?>
            <div class="toy-grid">
                <?php $cardMode = 'shop'; ?>
                <?php foreach ($toys as $toy): ?>
                    <?php $cardImages = shopToyImages($toy, $extraImages[(int) $toy['id']] ?? []); ?>
                    <?php require __DIR__ . '/../includes/toy_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="toys-empty">
                <span aria-hidden="true">🧸</span>
                <h2>No toys for this age yet</h2>
                <p>Try another age above, or go back and pick another toy type.</p>
                <a class="btn btn-primary" href="<?= BASE_URL ?>/products/categories.php">Back to categories</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
