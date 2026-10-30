<?php
// Navbar search: products/find.php?q=<toy name>
// The storefront navbar (includes/shop_navbar.php) sends its search box here.
//   - the name of an existing toy  -> straight to that toy's page (products/details.php?id=..)
//   - empty search                 -> a friendly "type a toy name" message
//   - a name that doesn't exist    -> a clear "Not found" message on this page
//                                     (+ toys whose name contains the text, if any)
// Toy names are read from the products table through ToyCatalog.
// products/search.php (the "Search & Explore" page) is left as it is.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';

$catalog = new ToyCatalog($pdo);

// Same limit as the search box (maxlength="60"); extra spaces are removed.
$query = $_GET['q'] ?? '';
$query = is_string($query) ? trim(preg_replace('/\s+/u', ' ', $query) ?? '') : '';
$query = mb_substr($query, 0, 60);

$suggestions = [];

if ($query !== '') {
    $toy = $catalog->findToyByExactName($query);
    if ($toy) {
        // Real id from the database -> always a valid link
        header('Location: ' . BASE_URL . '/products/details.php?id=' . (int) $toy['id']);
        exit;
    }
    $suggestions = $catalog->findToysByNamePart($query, 4);
}

$pageTitle  = $query === '' ? 'Search' : 'Not found';
$activePage = '';
$pageStyles = ['toy-details.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container details-missing">
    <?php if ($query === ''): ?>
        <span aria-hidden="true">🔎</span>
        <h1>Type a toy name to search</h1>
        <p>Write the name of a toy in the search box above, then press Enter.</p>
    <?php else: ?>
        <span aria-hidden="true">🧸</span>
        <h1>Not Found</h1>
        <p>We couldn't find a toy named “<?= htmlspecialchars($query) ?>”. Check the spelling or browse all toys.</p>
    <?php endif; ?>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Browse all toys</a>
</section>

<?php if (!empty($suggestions)): ?>
    <section class="shop-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Did you mean</span>
                    <h2>Toys with “<?= htmlspecialchars($query) ?>” in their name</h2>
                </div>
            </div>
            <div class="toy-grid">
                <?php $cardMode = 'landing'; ?>
                <?php foreach ($suggestions as $toy): ?>
                    <?php $cardImages = null; ?>
                    <?php require __DIR__ . '/../includes/toy_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
