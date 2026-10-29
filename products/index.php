<?php
// Toys page: products/index.php
// Optional filters in the URL (they can be combined):
//   ?category=<category slug>   sidebar "Toy types"
//   ?age=<age group slug>       Age dropdown (the landing page age cards link here too)
//   ?gender=girls|boys|unisex   Gender dropdown
// Cards: arrows on the picture browse all the toy's images; "How to play"
// is only shown on products/details.php.
// Page files: assets/css/toys-page.css (+ storefront.css), assets/js/storefront.js
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../classes/AgeGroup.php';

$catalog       = new ToyCatalog($pdo);
$ageGroupModel = new AgeGroup($pdo);

$categories = $catalog->getCategoriesWithToyCount();
$ageGroups  = $ageGroupModel->getAll();
usort($ageGroups, fn($a, $b) => [(int) $a['sort_order'], (int) $a['min_age']] <=> [(int) $b['sort_order'], (int) $b['min_age']]);
$genders    = ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex'];

// ---------- Read the filters (only values that really exist are used) ----------
$filters        = ['category' => '', 'age' => '', 'gender' => ''];
$activeCategory = null;
$activeAge      = null;

$categorySlug = (string) ($_GET['category'] ?? '');
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory      = $category;
        $filters['category'] = $categorySlug;
    }
}

$ageSlug = (string) ($_GET['age'] ?? '');
foreach ($ageGroups as $group) {
    if ($group['slug'] === $ageSlug) {
        $activeAge      = $group;
        $filters['age'] = $ageSlug;
    }
}

$gender = (string) ($_GET['gender'] ?? '');
if (isset($genders[$gender])) {
    $filters['gender'] = $gender;
}

// Toys page link that keeps the other filters.
function toysUrl(array $filters, array $change = []): string
{
    $query = array_filter(array_merge($filters, $change), fn($value) => $value !== '');

    return BASE_URL . '/products/index.php' . ($query ? '?' . http_build_query($query) : '');
}

// Bootstrap Icons class saved by the admin, e.g. "bi bi-palette".
function categoryIconClass(?string $icon): string
{
    $icon = trim((string) $icon);

    return preg_match('/^bi bi-[a-z0-9-]+$/', $icon) ? $icon : 'bi bi-box-seam';
}

// ---------- Toys + all their images (for the ‹ › arrows) ----------
$toys = $catalog->getFilteredToys([
    'category' => $filters['category'],
    'age'      => $activeAge,
    'gender'   => $filters['gender'],
]);
$extraImages = $catalog->getImagePathsForToys(array_column($toys, 'id'));

// ---------- Heading ----------
$minAge   = $ageGroups ? min(array_map('intval', array_column($ageGroups, 'min_age'))) : 3;
$maxAge   = $ageGroups ? max(array_map('intval', array_column($ageGroups, 'max_age'))) : 12;
$heading  = $activeCategory ? $activeCategory['name'] : 'All toys';
$subtitle = $activeCategory && !empty($activeCategory['description'])
    ? $activeCategory['description']
    : 'Toys for every age from ' . $minAge . ' to ' . $maxAge . '.';
$allCount = array_sum(array_column($categories, 'toy_count'));

$pageTitle  = $heading;
$activePage = 'toys';
$pageStyles = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'toys-page.css',
];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<div class="toys-layout">
    <!-- ============ Full-height sidebar on the left ============ -->
    <aside class="toys-sidebar" id="toy-types" aria-label="Toy types">
        <div class="toys-sidebar-inner">
            <h2 class="toys-sidebar-title">Toy types</h2>
            <ul class="cat-list">
                <li>
                    <a class="cat-link <?= $activeCategory ? '' : 'is-active' ?>"
                       href="<?= toysUrl($filters, ['category' => '']) ?>"
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
                           href="<?= toysUrl($filters, ['category' => $category['slug']]) ?>"
                           <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <span class="cat-icon"><i class="<?= htmlspecialchars(categoryIconClass($category['icon'])) ?>" aria-hidden="true"></i></span>
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
                <a href="#toy-types">Categories</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?= htmlspecialchars($heading) ?></span>
            </nav>
            <h1><?= htmlspecialchars($heading) ?></h1>
            <p><?= htmlspecialchars($subtitle) ?></p>
        </section>

        <div class="toys-content">
            <!-- ============ Filters ============ -->
            <form class="filter-bar" action="<?= BASE_URL ?>/products/index.php" method="get" data-filter-form>
                <?php if ($filters['category'] !== ''): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($filters['category']) ?>">
                <?php endif; ?>

                <div class="filter-field">
                    <label for="filter-age">Age</label>
                    <select id="filter-age" name="age">
                        <option value="">All ages</option>
                        <?php foreach ($ageGroups as $group): ?>
                            <option value="<?= htmlspecialchars($group['slug']) ?>" <?= $filters['age'] === $group['slug'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($group['name']) ?> (<?= (int) $group['min_age'] ?>–<?= (int) $group['max_age'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="filter-gender">Gender</label>
                    <select id="filter-gender" name="gender">
                        <option value="">All</option>
                        <?php foreach ($genders as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filters['gender'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <noscript><button class="btn btn-primary" type="submit">Apply</button></noscript>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/products/index.php">Reset filters</a>
            </form>

            <p class="result-count" role="status">
                <?= count($toys) ?> <?= count($toys) === 1 ? 'toy' : 'toys' ?> found
            </p>

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
                    <h2>No toys match these filters</h2>
                    <p>Try another age or toy type, or start again.</p>
                    <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Show all toys</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
