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
    <section class="category-gift" aria-labelledby="gift-heading">
        <div class="category-gift-intro">
            <span class="browse-eyebrow">A little help choosing</span>
            <h2 id="gift-heading">Find a gift they'll love</h2>
            <p>Start with their age and your budget. We'll show you the toys that fit.</p>
            <div class="category-discovery-links">
                <a href="<?= BASE_URL ?>/products/index.php?offers=1">Explore offers <span aria-hidden="true">→</span></a>
                <a href="<?= BASE_URL ?>/products/index.php?sort=newest">See new arrivals <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <form class="category-gift-form" action="<?= BASE_URL ?>/products/index.php" method="get">
            <div><label for="gift-age">Who are you shopping for?</label>
                <select id="gift-age" name="age">
                    <option value="">All ages</option>
                    <?php foreach ($ageCards as $slug => $age): ?>
                    <option value="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($age['title'] . ' (' . ageCardRange($age) . ' years)') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label for="gift-budget">Your budget</label>
                <select id="gift-budget" name="price">
                    <option value="">Any budget</option>
                    <option value="under-10">Under 10 JOD</option>
                    <option value="10-25">10–25 JOD</option>
                    <option value="25-50">25–50 JOD</option>
                    <option value="50-100">50–100 JOD</option>
                    <option value="over-100">100 JOD &amp; more</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Find toys</button>
        </form>
    </section>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
