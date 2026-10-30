<?php
// Categories page: products/categories.php
// Pick a toy type in the sidebar -> the page shows age cards for that type:
//   0–3, 4–6, 7–10 and 10–12 years.
// Pick an age card -> the toys of that type and age appear under the cards.
//
// Optional values in the URL:
//   ?category=<category slug>   sidebar "Toy types" (empty = all toys)
//   ?age=0-3|4-6|7-10|10-12     age card
//
// This page only READS data (ToyCatalog) and reuses the shared parts as they are:
//   includes/shop_header.php (navbar), includes/footer.php (footer),
//   includes/toy_card.php (toy cards), assets/css/toys-page.css (sidebar + heading).
// Page-only files: assets/css/categories-page.css
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';

$catalog    = new ToyCatalog($pdo);
$categories = $catalog->getCategoriesWithToyCount();

// ---------- The 4 age cards ----------
// Fixed on this page (not taken from the age_groups table), so the
// admin's age groups and the toys page filters stay exactly as they are.
$ageCards = [
    '0-3'   => ['min' => 0,  'max' => 3,  'title' => 'Babies & Toddlers', 'text' => 'Soft, safe toys for little hands exploring colors, sounds and textures.', 'color' => 'blue',   'emoji' => '🧸'],
    '4-6'   => ['min' => 4,  'max' => 6,  'title' => 'Preschoolers',      'text' => 'Playful toys that build first skills: shapes, letters and imagination.',  'color' => 'green',  'emoji' => '🖍️'],
    '7-10'  => ['min' => 7,  'max' => 10, 'title' => 'Young Explorers',   'text' => 'Building sets, puzzles and games for curious, growing minds.',            'color' => 'orange', 'emoji' => '🔬'],
    '10-12' => ['min' => 10, 'max' => 12, 'title' => 'Big Kids & Tweens', 'text' => 'Challenging kits, sports and tech toys for bigger adventures.',            'color' => 'pink',   'emoji' => '🤖'],
];

// ---------- Read the URL (only values that really exist are used) ----------
$activeCategory = null;
$categorySlug   = (string) ($_GET['category'] ?? '');
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory = $category;
    }
}
$categorySlug = $activeCategory ? $activeCategory['slug'] : '';

$ageKey    = (string) ($_GET['age'] ?? '');
$activeAge = $ageCards[$ageKey] ?? null;
if ($activeAge === null) {
    $ageKey = '';
}

// Link to this page. Changing the toy type starts again from the age cards.
function categoriesPageUrl(string $categorySlug, string $ageKey = ''): string
{
    $query = array_filter(['category' => $categorySlug, 'age' => $ageKey], fn($value) => $value !== '');

    return BASE_URL . '/products/categories.php' . ($query ? '?' . http_build_query($query) : '');
}

// Bootstrap Icons class saved by the admin, e.g. "bi bi-palette".
// Same rule as the toys page, so both sidebars show the same icons.
function categoriesPageIcon(?string $icon): string
{
    $icon = trim((string) $icon);

    return preg_match('/^bi bi-[a-z0-9-]+$/', $icon) ? $icon : 'bi bi-box-seam';
}

// ---------- Toys of the chosen type for every age card ----------
// A toy fits an age card when the two age ranges overlap
// (the same rule ToyCatalog already uses for the toys page filter).
$toysByAge = [];
foreach ($ageCards as $key => $card) {
    $toysByAge[$key] = $catalog->getFilteredToys([
        'category' => $categorySlug,
        'age'      => ['min_age' => $card['min'], 'max_age' => $card['max']],
    ]);
}

// Toy grid under the cards (only after an age card is chosen)
$toys        = $activeAge ? $toysByAge[$ageKey] : [];
$extraImages = $toys ? $catalog->getImagePathsForToys(array_column($toys, 'id')) : [];

// ---------- Heading ----------
$typeName = $activeCategory ? $activeCategory['name'] : 'All toys';
$heading  = $typeName;
$subtitle = $activeCategory && !empty($activeCategory['description'])
    ? $activeCategory['description']
    : 'Choose a toy type, then pick your child’s age.';
$allCount = array_sum(array_column($categories, 'toy_count'));
$ageLabel = $activeAge ? $activeAge['min'] . '–' . $activeAge['max'] . ' years' : '';

$pageTitle  = $activeAge ? $typeName . ' · ' . $ageLabel : 'Categories · ' . $typeName;
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
                <?php if ($activeAge): ?>
                    <a href="<?= categoriesPageUrl($categorySlug) ?>"><?= htmlspecialchars($typeName) ?></a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page"><?= $ageLabel ?></span>
                <?php else: ?>
                    <span aria-current="page"><?= htmlspecialchars($typeName) ?></span>
                <?php endif; ?>
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
                        <?php
                        $count    = count($toysByAge[$key]);
                        $isChosen = $ageKey === $key;
                        ?>
                        <a class="agecat-card agecat-<?= $card['color'] ?> <?= $isChosen ? 'is-active' : '' ?>"
                           href="<?= categoriesPageUrl($categorySlug, $key) ?>#age-toys"
                           <?= $isChosen ? 'aria-current="true"' : '' ?>>
                            <div class="agecat-top">
                                <span class="agecat-pic" aria-hidden="true"><?= $card['emoji'] ?></span>
                                <span class="agecat-range"><?= $card['min'] ?>–<?= $card['max'] ?> <small>years</small></span>
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

            <!-- ============ Toys of the chosen age ============ -->
            <section class="agecat-results" id="age-toys" aria-live="polite">
                <?php if (!$activeAge): ?>
                    <p class="agecat-hint">
                        <span aria-hidden="true">👆</span>
                        Pick an age card to see the <?= $activeCategory ? htmlspecialchars($typeName) : 'toys' ?> that fit it.
                    </p>
                <?php else: ?>
                    <div class="agecat-results-head">
                        <p class="result-count" role="status">
                            <?= count($toys) ?> <?= count($toys) === 1 ? 'toy' : 'toys' ?> for ages <?= $ageLabel ?>
                        </p>
                        <a class="btn btn-outline btn-sm" href="<?= categoriesPageUrl($categorySlug) ?>#age-cards">Show all ages</a>
                    </div>

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
                            <p>Try another age card or another toy type.</p>
                            <a class="btn btn-primary" href="<?= categoriesPageUrl('') ?>">Show all toys</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
