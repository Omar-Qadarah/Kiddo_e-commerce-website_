<?php
// Categories page: products/categories.php
// Pick a toy type in the sidebar -> the page shows age cards for that type:
//   0–3, 4–6, 7–10, 10–12 and 12+ years.
// Pick an age card -> a separate page (products/age_toys.php) lists only
// the toys of that age (and of the chosen toy type).
//
// Optional value in the URL:
//   ?category=<category slug>   sidebar "Toy types" (empty = all toys)
//
// This page only READS data (ToyCatalog) and reuses the shared parts as they are:
//   includes/shop_header.php (navbar), includes/footer.php (footer),
//   includes/toy_card.php (toy cards), assets/css/toys-page.css (sidebar + heading).
// Page-only files: assets/css/categories-page.css
// The age cards themselves are defined in includes/age_cards.php.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../includes/age_cards.php';

$catalog    = new ToyCatalog($pdo);
$categories = $catalog->getCategoriesWithToyCount();

// ---------- Read the URL (only values that really exist are used) ----------
$activeCategory = null;
$categorySlug   = (string) ($_GET['category'] ?? '');
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory = $category;
    }
}
$categorySlug = $activeCategory ? $activeCategory['slug'] : '';

// Old links like categories.php?age=4-6 now open the separate age page.
$oldAgeKey = (string) ($_GET['age'] ?? '');
if (isset($ageCards[$oldAgeKey])) {
    header('Location: ' . ageToysUrl($oldAgeKey, $categorySlug));
    exit;
}

// Link to this page (optionally for one toy type).
function categoriesPageUrl(string $categorySlug): string
{
    return BASE_URL . '/products/categories.php' . ($categorySlug !== '' ? '?' . http_build_query(['category' => $categorySlug]) : '');
}

// Bootstrap Icons class saved by the admin, e.g. "bi bi-palette".
// Same rule as the toys page, so both sidebars show the same icons.
function categoriesPageIcon(?string $icon): string
{
    $icon = trim((string) $icon);

    return preg_match('/^bi bi-[a-z0-9-]+$/', $icon) ? $icon : 'bi bi-box-seam';
}

// ---------- How many toys of the chosen type fit every age card ----------
// A toy fits an age card when the two age ranges overlap
// (the same rule ToyCatalog already uses for the toys page filter).
$toysByAge = [];
foreach ($ageCards as $key => $card) {
    $toysByAge[$key] = $catalog->getFilteredToys([
        'category' => $categorySlug,
        'age'      => ['min_age' => $card['min'], 'max_age' => $card['max']],
    ]);
}

// ---------- Heading ----------
$typeName = $activeCategory ? $activeCategory['name'] : 'All toys';
$heading  = $typeName;
$subtitle = $activeCategory && !empty($activeCategory['description'])
    ? $activeCategory['description']
    : 'Choose a toy type, then pick your child’s age.';
$allCount = array_sum(array_column($categories, 'toy_count'));

$pageTitle  = 'Categories · ' . $typeName;
$activePage = 'categories';
$pageStyles = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'toys-page.css',
    'categories-page.css',
];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<div class="toys-layout">
    <!-- ============ Full-height sidebar on the left (same as the toys page) ============ -->
    <aside class="toys-sidebar" id="toy-types" aria-label="Toy types">
        <div class="toys-sidebar-inner">
            <h2 class="toys-sidebar-title">Toy types</h2>
            <ul class="cat-list">
                <li>
                    <a class="cat-link <?= $activeCategory ? '' : 'is-active' ?>"
                       href="<?= categoriesPageUrl('') ?>"
                       <?= $activeCategory ? '' : 'aria-current="page"' ?>>
                        <span class="cat-icon"><i class="bi bi-stars" aria-hidden="true"></i></span>
                        <span class="cat-name">All toys</span>
                        <span class="cat-count"><?= (int) $allCount ?></span>
                    </a>
                </li>
                <?php foreach ($categories as $category): ?>
                    <?php $isActive = $activeCategory && (int) $activeCategory['id'] === (int) $category['id']; ?>
                    <li>
                        <a class="cat-link <?= $isActive ? 'is-active' : '' ?>"
                           href="<?= categoriesPageUrl($category['slug']) ?>"
                           <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <span class="cat-icon"><i class="<?= htmlspecialchars(categoriesPageIcon($category['icon'])) ?>" aria-hidden="true"></i></span>
                            <span class="cat-name"><?= htmlspecialchars($category['name']) ?></span>
                            <?php if ((int) $category['toy_count'] > 0): ?>
                                <span class="cat-count"><?= (int) $category['toy_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <div class="toys-main">
        <!-- ============ Heading ============ -->
        <section class="toys-hero">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>/index.php">Home</a>
                <span aria-hidden="true">/</span>
                <a href="<?= categoriesPageUrl('') ?>">Categories</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?= htmlspecialchars($typeName) ?></span>
            </nav>
            <h1><?= htmlspecialchars($heading) ?></h1>
            <p><?= htmlspecialchars($subtitle) ?></p>
        </section>

        <div class="toys-content">
            <!-- ============ Age cards ============ -->
            <section class="agecat-section" id="age-cards" aria-labelledby="agecat-title">
                <div class="agecat-head">
                    <span class="agecat-eyebrow">Shop by age</span>
                    <h2 id="agecat-title"><?= htmlspecialchars($typeName) ?> for every age</h2>
                </div>

                <div class="agecat-grid">
                    <?php foreach ($ageCards as $key => $card): ?>
                        <?php $count = count($toysByAge[$key]); ?>
                        <a class="agecat-card agecat-<?= $card['color'] ?>"
                           href="<?= htmlspecialchars(ageToysUrl($key, $categorySlug)) ?>">
                            <div class="agecat-top">
                                <span class="agecat-pic" aria-hidden="true"><?= $card['emoji'] ?></span>
                                <span class="agecat-range"><?= ageCardRange($card) ?> <small>years</small></span>
                            </div>
                            <div class="agecat-body">
                                <h3><?= htmlspecialchars($card['title']) ?></h3>
                                <p><?= htmlspecialchars($card['text']) ?></p>
                                <div class="agecat-foot">
                                    <span><?= $count ?> <?= $count === 1 ? 'toy' : 'toys' ?></span>
                                    <span class="agecat-go" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <p class="agecat-hint">
                <span aria-hidden="true">👆</span>
                Pick an age card to open a page with only the <?= $activeCategory ? htmlspecialchars($typeName) : 'toys' ?> for that age.
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
