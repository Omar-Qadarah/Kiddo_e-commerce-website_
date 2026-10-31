<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../includes/age_cards.php';
if (isset($_GET['age']) && is_string($_GET['age']) && (isset($ageCards[$_GET['age']]) || isset($legacyAgeCards[$_GET['age']]))) {
    header('Location: ' . BASE_URL . '/products/age_toys.php?' . http_build_query(['age' => $_GET['age'], 'category' => is_string($_GET['category'] ?? null) ? $_GET['category'] : '']));
    exit;
}
if (!empty($_GET['category']) && is_string($_GET['category'])) {
    header('Location: ' . BASE_URL . '/products/index.php?' . http_build_query(['category' => $_GET['category']]));
    exit;
}
$catalog = new ToyCatalog($pdo);
$categories = $catalog->getShopCategories();
$pageTitle = 'Categories';
$activePage = 'categories';
$pageStyles = ['landing.css'];
require __DIR__ . '/../includes/shop_header.php';
?>
<section class="container category-discovery category-hub">
    <span class="browse-eyebrow">Shop by category</span>
    <h1>Find the perfect toy</h1>

    <?php require __DIR__ . '/../includes/category_grid.php'; ?>
    <?php if (!$categories): ?><p>New toy collections are on their way. <a href="<?= BASE_URL ?>/products/index.php">Explore all toys</a></p><?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
