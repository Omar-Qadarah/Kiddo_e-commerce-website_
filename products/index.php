<?php
// Toys page: products/index.php
// Optional filters in the URL (they can be combined):
//   ?category=<category slug>   sidebar "Toy types"
//   ?age=<age group slug>       Sidebar age filter (landing-page age links also work)
//   ?gender=girls|boys|unisex   Gender dropdown
//   ?price=<price range key>    Price dropdown (keys in $priceRanges below)
// Cards: arrows on the picture browse all the toy's images; "How to play"
// is only shown on products/details.php.
// Page files: assets/css/toys-page.css (+ storefront.css), assets/js/storefront.js
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../includes/shop_pagination.php';
require_once __DIR__ . '/../includes/age_cards.php';

$catalog       = new ToyCatalog($pdo);

$isOffersPage = ($_GET['offers'] ?? '') === '1';
$categories = $isOffersPage
    ? $catalog->getCategoriesWithDiscountedToyCount()
    : $catalog->getShopCategories();
$ageGroups  = $storeAgeGroups;
$genders    = ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex'];

// Price ranges in JOD (after discount). min is included, max is not; null = no limit.
$priceRanges = [
    'under-10'  => ['label' => 'Under 10 JOD',  'min' => null, 'max' => 10],
    '10-25'     => ['label' => '10 – 25 JOD',   'min' => 10,   'max' => 25],
    '25-50'     => ['label' => '25 – 50 JOD',   'min' => 25,   'max' => 50],
    '50-100'    => ['label' => '50 – 100 JOD',  'min' => 50,   'max' => 100],
    'over-100'  => ['label' => '100 JOD & more', 'min' => 100, 'max' => null],
];

// ---------- Read the filters (only values that really exist are used) ----------
$filters        = ['category' => '', 'age' => '', 'gender' => '', 'price' => '', 'q' => is_string($_GET['q'] ?? null) ? mb_substr(preg_replace('/\s+/u', ' ', trim($_GET['q'])), 0, 60) : '', 'sort' => 'newest', 'stock' => '', 'offers' => ''];
$activeCategory = null;
$activeAge      = null;
$activePrice    = null;

$categorySlug = is_string($_GET['category'] ?? null) ? $_GET['category'] : '';
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory      = $category;
        $filters['category'] = $categorySlug;
    }
}

if (!$activeCategory && $categorySlug !== '') {
    foreach ($catalog->getCategoriesWithToyCount() as $oldCategory) {
        if ($oldCategory['slug'] === $categorySlug) { $activeCategory = $oldCategory; $filters['category'] = $categorySlug; break; }
    }
}

$ageSlug = is_string($_GET['age'] ?? null) ? $_GET['age'] : '';
foreach ($ageGroups as $group) {
    if ($group['slug'] === $ageSlug) {
        $activeAge      = $group;
        $filters['age'] = $ageSlug;
    }
}

// Compact age buttons reuse the existing age-card ranges. Legacy database age links still work.
$requestedAge = $ageCards[$ageSlug] ?? null;
if ($requestedAge) {
    $activeAge = ['min_age' => $requestedAge['min'], 'max_age' => $requestedAge['max']];
    $filters['age'] = $ageSlug;
}

$gender = is_string($_GET['gender'] ?? null) ? $_GET['gender'] : '';
if (isset($genders[$gender])) {
    $filters['gender'] = $gender;
}

$priceKey = is_string($_GET['price'] ?? null) ? $_GET['price'] : '';
if (isset($priceRanges[$priceKey])) {
    $activePrice      = $priceRanges[$priceKey];
    $filters['price'] = $priceKey;
}

$sortLabels = ['newest' => 'Newest first', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name_asc' => 'Name: A–Z'];
$sortKey = is_string($_GET['sort'] ?? null) ? $_GET['sort'] : '';
if (isset($sortLabels[$sortKey])) $filters['sort'] = $sortKey;
$filters['stock'] = ''; // Availability checkbox was removed from the customer UI.
$filters['offers'] = $isOffersPage ? '1' : '';

// Toys page link that keeps the other filters.
function toysUrl(array $filters, array $change = []): string
{
    $query = array_filter(array_merge($filters, $change), fn($value) => $value !== '');

    return BASE_URL . '/products/' . (basename($_SERVER['SCRIPT_NAME']) === 'find.php' ? 'find.php' : 'index.php') . ($query ? '?' . http_build_query($query) : '');
}

// Bootstrap Icons class saved by the admin, e.g. "bi bi-palette".
function categoryIconClass(?string $icon): string
{
    $icon = trim((string) $icon);

    return preg_match('/^bi bi-[a-z0-9-]+$/', $icon) ? $icon : 'bi bi-box-seam';
}

// ---------- Toys + all their images (for the ‹ › arrows) ----------
$catalogFilters = [
    'category' => $filters['category'],
    'age'      => $activeAge,
    'gender'   => $filters['gender'],
    'price'    => $activePrice,
    'q' => $filters['q'],
    'sort' => $filters['sort'],
    'stock' => $filters['stock'],
    'offers' => $filters['offers'],
];
$totalToys = $catalog->countFilteredToys($catalogFilters);
$pagination = shopPagination($totalToys);
$toys = $catalog->getFilteredToys($catalogFilters, SHOP_PAGE_SIZE, $pagination['offset']);
$extraImages = $catalog->getImagePathsForToys(array_column($toys, 'id'));

// ---------- Heading ----------
$minAge   = $ageGroups ? min(array_map('intval', array_column($ageGroups, 'min_age'))) : 3;
$maxAge   = $ageGroups ? max(array_map('intval', array_column($ageGroups, 'max_age'))) : 12;
$heading  = $activeCategory ? $activeCategory['name'] : ($filters['offers'] ? 'Little prices, big smiles' : 'Find their next favourite');
$subtitle = $activeCategory && !empty($activeCategory['description'])
    ? $activeCategory['description']
    : 'A little imagination starts here. Explore by age, interest and budget.';
$allCount = array_sum(array_column($categories, 'toy_count'));

if ($requestedAge && !$activeCategory && !$filters['offers']) {
    $heading = $requestedAge['title'] . ' (' . ageCardRange($requestedAge) . ' years)';
    $subtitle = $requestedAge['text'];
}
$heading = $filters['q'] !== '' ? 'Search results for “' . $filters['q'] . '”' : $heading;
$pageTitle  = $heading;
$activePage = $filters['offers'] ? 'offers' : 'toys';
$catalogLayout = true;
$pageStyles = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'toys-page.css',
    'categories-page.css',
    'browsing.css',
];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<div class="toys-layout toys-pill-page catalog-page">
    <!-- ============ Full-height sidebar on the left ============ -->
    <details class="catalog-sidebar" open>
        <summary>Filters &amp; categories <span aria-hidden="true">⌄</span></summary>
        <div class="catalog-sidebar-content">
    <?php $categoryUrl = fn($slug) => toysUrl($filters, ['category' => $slug]); require __DIR__ . '/../includes/shop_category_sidebar.php'; ?>
            <section class="catalog-filters" aria-labelledby="catalog-filter-title">
                <h2 id="catalog-filter-title">Filters</h2>
            <form class="filter-bar" action="<?= htmlspecialchars(strtok(toysUrl([]), '?')) ?>" method="get" data-filter-form>
                <?php foreach (['q', 'sort'] as $key): if ($filters[$key] === '') continue; ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($filters[$key]) ?>">
                <?php endforeach; ?>
                <?php if ($filters['category'] !== ''): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($filters['category']) ?>">
                <?php endif; ?>

                <div class="filter-field">
                    <label for="filter-age">Age</label>
                    <select id="filter-age" name="age">
                        <option value="">All ages</option>
                        <?php foreach ($ageCards as $key => $ageCard): ?>
                        <option value="<?= htmlspecialchars($key) ?>" <?= $filters['age'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($ageCard['title']) ?> (<?= ageCardRange($ageCard) ?> Years)</option>
                        <?php endforeach; ?>
                        <?php if ($activeAge && !isset($ageCards[$filters['age']])): ?>
                        <option value="<?= htmlspecialchars($filters['age']) ?>" selected><?= (int)$activeAge['min_age'] ?>–<?= (int)$activeAge['max_age'] ?> Years</option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php if ($filters['offers']): ?><input type="hidden" name="offers" value="1"><?php endif; ?>

                <div class="filter-field">
                    <label for="filter-gender">Gender</label>
                    <select id="filter-gender" name="gender">
                        <option value="">All</option>
                        <?php foreach ($genders as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filters['gender'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="filter-price">Price</label>
                    <select id="filter-price" name="price">
                        <option value="">Any price</option>
                        <?php foreach ($priceRanges as $value => $range): ?>
                            <option value="<?= $value ?>" <?= $filters['price'] === $value ? 'selected' : '' ?>><?= $range['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-primary browse-apply" type="submit">Apply filters</button>
                    <a class="btn btn-outline" href="<?= BASE_URL ?>/products/index.php<?= $filters['offers'] ? '?offers=1' : '' ?>">Clear all</a>
                </div>
            </form>
            </section>
        </div>
    </details>

    <div class="toys-main">
        <div class="toys-content">
            <div class="active-filters" aria-label="Applied filters">
                <?php
                $chipLabels = [
                    'category' => $activeCategory['name'] ?? '',
                    'age' => $requestedAge ? $requestedAge['title'] . ' (' . ageCardRange($requestedAge) . ')' : '',
                    'gender' => $genders[$filters['gender']] ?? '',
                    'price' => $activePrice['label'] ?? '',
                    'q' => $filters['q'] !== '' ? 'Search: ' . $filters['q'] : '',
                    'stock' => $filters['stock'] ? 'In stock' : '',
                    'offers' => '', // Offers is page context, not a removable customer checkbox.
                ];
                foreach ($chipLabels as $key => $label): if ($label === '') continue; ?>
                    <a href="<?= htmlspecialchars(toysUrl($filters, [$key => ''])) ?>" aria-label="<?= htmlspecialchars('Remove filter: ' . $label) ?>"><?= htmlspecialchars($label) ?> <span aria-hidden="true">×</span></a>
                <?php endforeach; ?>
            </div>
            <div class="catalog-toolbar">
                <p class="result-count" role="status">
                    <?php $entity = $filters['offers'] ? ($totalToys === 1 ? 'offer' : 'offers') : ($totalToys === 1 ? 'toy' : 'toys'); ?>
                    <?php if ($totalToys): ?>
                        Showing <?= $pagination['offset'] + 1 ?><?php if ($totalToys > 1): ?>–<?= min($pagination['offset'] + SHOP_PAGE_SIZE, $totalToys) ?><?php endif; ?> of <strong><?= $totalToys ?></strong> <?= $entity ?>
                    <?php else: ?>0 <?= $entity ?> found<?php endif; ?>
                </p>
                <form class="catalog-sort" action="<?= htmlspecialchars(strtok(toysUrl([]), '?')) ?>" method="get">
                    <?php foreach ($filters as $key => $value): if ($key === 'sort' || $value === '') continue; ?>
                        <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php endforeach; ?>
                    <label for="filter-sort">Sort by</label>
                    <select id="filter-sort" name="sort">
                        <?php foreach ($sortLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline" type="submit">Sort</button>
                </form>
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
                    <h2><?= $filters['q'] !== '' ? 'No toys found for “' . htmlspecialchars($filters['q']) . '”' : 'No toys match these filters' ?></h2>
                    <p>Check your spelling, try fewer words, or remove a filter.</p><a class="btn btn-outline" href="<?= BASE_URL ?>/products/categories.php">Browse categories</a>
                    <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php<?= $filters['offers'] ? '?offers=1' : '' ?>">Clear filters</a>
                </div>
            <?php endif; ?>
        <?php renderShopPagination($pagination, $filters, true); ?>
        </div>
    </div>
</div>

<script>
// Native disclosure on mobile; desktop keeps the sidebar open.
(function () {
    const sidebar = document.querySelector('.catalog-sidebar');
    const mobile = window.matchMedia('(max-width: 820px)');
    const syncSidebar = () => { sidebar.open = !mobile.matches; };
    syncSidebar();
    mobile.addEventListener('change', syncSidebar);
})();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
