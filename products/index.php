<?php
// Toys page: products/index.php
// Optional filters in the URL (they can be combined):
//   ?category=<category slug>   sidebar "Toy types"
//   ?age=<age group slug>       Age dropdown (the home page age cards link here too)
//   ?gender=boys|girls|unisex   Gender dropdown
// Page files: assets/css/toys.css + assets/css/shop.css, assets/js/toys.js

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductImage.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/AgeGroup.php';

$productModel  = new Product($pdo);
$imageModel    = new ProductImage($pdo);
$categoryModel = new Category($pdo);
$ageGroupModel = new AgeGroup($pdo);

$categories = $categoryModel->getAllWithToyCount();
$ageGroups  = $ageGroupModel->getAll();
$genders    = ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex only'];

// ---------- Read the filters (only values that really exist are used) ----------
$filters = ['category' => '', 'age' => '', 'gender' => ''];

$categorySlug = (string) ($_GET['category'] ?? '');
$activeCategory = null;
foreach ($categories as $category) {
    if ($category['slug'] === $categorySlug) {
        $activeCategory = $category;
        $filters['category'] = $categorySlug;
    }
}

$ageSlug = (string) ($_GET['age'] ?? '');
foreach ($ageGroups as $group) {
    if ($group['slug'] === $ageSlug) {
        $filters['age'] = $ageSlug;
    }
}

$gender = (string) ($_GET['gender'] ?? '');
if (isset($genders[$gender])) {
    $filters['gender'] = $gender;
}

// Builds a Toys page link that keeps the other filters.
function shopUrl(array $filters, array $change = []): string
{
    $query = array_filter(array_merge($filters, $change), fn($value) => $value !== '');
    return BASE_URL . '/products/index.php' . ($query ? '?' . http_build_query($query) : '');
}

// ---------- Toys + their images (for the ‹ › arrows) ----------
$toys        = $productModel->getFilteredToys($filters);
$imagesByToy = $imageModel->getPathsForProducts(array_column($toys, 'id'));

// Image URLs for one toy: main image first, then the extra ones, no repeats.
function toyImageUrls(array $toy, array $imagesByToy): array
{
    $paths = array_merge([(string) $toy['image']], $imagesByToy[(int) $toy['id']] ?? []);
    $urls  = [];
    foreach ($paths as $path) {
        $path = ltrim(trim($path), '/');
        if ($path !== '') {
            $urls[$path] = BASE_URL . '/' . $path;
        }
    }
    return array_values($urls);
}

// ---------- Page heading ----------
$minAge = $ageGroups ? min(array_column($ageGroups, 'min_age')) : 3;
$maxAge = $ageGroups ? max(array_column($ageGroups, 'max_age')) : 12;
$heading  = $activeCategory ? $activeCategory['name'] : 'All toys';
$subtitle = $activeCategory && !empty($activeCategory['description'])
    ? $activeCategory['description']
    : 'Toys for every age from ' . (int) $minAge . ' to ' . (int) $maxAge . '.';
$allCount = array_sum(array_column($categories, 'toy_count'));

// Category icon: the Bootstrap Icons class saved by the admin (e.g. "bi bi-palette").
function categoryIcon(?string $icon): string
{
    $icon = trim((string) $icon);
    return preg_match('/^bi bi-[a-z0-9-]+$/', $icon) ? $icon : 'bi bi-box-seam';
}

$pageTitle   = $heading;
$activePage  = 'toys';
$pageStyles  = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'toys.css',
    'shop.css',
];
$pageScripts = ['toys.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============ Page heading ============ -->
<section class="shop-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/index.php">Home</a>
            <span aria-hidden="true">/</span>
            <?php if ($activeCategory): ?>
                <a href="<?= shopUrl($filters, ['category' => '']) ?>">Toys</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?= htmlspecialchars($activeCategory['name']) ?></span>
            <?php else: ?>
                <span aria-current="page">All toys</span>
            <?php endif; ?>
        </nav>
        <h1><?= htmlspecialchars($heading) ?></h1>
        <p><?= htmlspecialchars($subtitle) ?></p>
    </div>
</section>

<div class="shop">
    <!-- ============ Full-height categories sidebar ============ -->
    <aside class="shop-sidebar" aria-label="Toy types">
        <div class="shop-sidebar-inner">
            <h2 class="sidebar-title">Toy types</h2>
            <ul class="cat-list">
                <li>
                    <a class="cat-link <?= $activeCategory ? '' : 'is-active' ?>" href="<?= shopUrl($filters, ['category' => '']) ?>"
                       <?= $activeCategory ? '' : 'aria-current="page"' ?>>
                        <span class="cat-icon"><i class="bi bi-stars" aria-hidden="true"></i></span>
                        <span class="cat-name">All toys</span>
                        <span class="cat-count"><?= (int) $allCount ?></span>
                    </a>
                </li>
                <?php foreach ($categories as $category): ?>
                    <?php $isActive = $activeCategory && $activeCategory['id'] === $category['id']; ?>
                    <li>
                        <a class="cat-link <?= $isActive ? 'is-active' : '' ?>"
                           href="<?= shopUrl($filters, ['category' => $category['slug']]) ?>"
                           <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <span class="cat-icon"><i class="<?= htmlspecialchars(categoryIcon($category['icon'])) ?>" aria-hidden="true"></i></span>
                            <span class="cat-name"><?= htmlspecialchars($category['name']) ?></span>
                            <span class="cat-count"><?= (int) $category['toy_count'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <!-- ============ Filters + toy grid ============ -->
    <div class="shop-main">
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

            <div class="filter-actions">
                <button class="btn btn-primary" type="submit" data-filter-apply>Apply</button>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/products/index.php">Reset filters</a>
            </div>
        </form>

        <p class="result-count" role="status">
            <?= count($toys) ?> <?= count($toys) === 1 ? 'toy' : 'toys' ?> found
        </p>

        <?php if (!empty($toys)): ?>
            <div class="toy-grid shop-grid">
                <?php
                // Toys page cards: image with arrows, name, short description, rating, price
                $cardShowDescription = true;
                $cardShowPrice       = true;
                ?>
                <?php foreach ($toys as $toyIndex => $toy): ?>
                    <?php $cardImages = toyImageUrls($toy, $imagesByToy); ?>
                    <?php require __DIR__ . '/../includes/toy-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="shop-empty">
                <span aria-hidden="true">🧸</span>
                <h2>No toys match these filters</h2>
                <p>Try another age or toy type, or start again.</p>
                <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Show all toys</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
