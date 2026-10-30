<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/SearchHistory.php';


$historyModel = new SearchHistory($pdo);
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    checkCsrf();

    if ($userId > 0) {
        if ($_POST['action'] === 'clear_all') {
            $historyModel->clearAllHistory($userId);
        } elseif ($_POST['action'] === 'delete_item' && isset($_POST['search_id'])) {
            $historyModel->deleteSearchItem($userId, (int)$_POST['search_id']);
        }
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$ageGroupId = filter_input(INPUT_GET, 'age_group', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$gender = (isset($_GET['gender']) && in_array($_GET['gender'], ['boys', 'girls', 'unisex'], true)) ? $_GET['gender'] : '';
$minPrice = filter_input(INPUT_GET, 'min_price', FILTER_VALIDATE_FLOAT);
$maxPrice = filter_input(INPUT_GET, 'max_price', FILTER_VALIDATE_FLOAT);

$allowedSorts = [
    'newest'     => 'p.created_at DESC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc'   => 'p.name ASC'
];
$sort = $_GET['sort'] ?? 'newest';
$orderBy = $allowedSorts[$sort] ?? 'p.created_at DESC';

$categoriesStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$ageGroupsStmt = $pdo->query("SELECT id, name, min_age, max_age FROM age_groups ORDER BY sort_order ASC");
$ageGroups = $ageGroupsStmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT p.id, p.category_id, p.name, p.description, p.price, p.stock, 
               p.gender, p.min_age, p.max_age, p.image, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1";

$params = [];

if ($searchTerm !== '') {
    $sql .= " AND (p.name LIKE :search_name OR p.description LIKE :search_desc)";
    $params[':search_name'] = "%{$searchTerm}%";
    $params[':search_desc'] = "%{$searchTerm}%";
}

if ($categoryId) {
    $sql .= " AND p.category_id = :cat_id";
    $params[':cat_id'] = $categoryId;
}

if ($gender !== '') {
    $sql .= " AND (p.gender = :gender OR p.gender = 'unisex')";
    $params[':gender'] = $gender;
}

if ($minPrice !== false && $minPrice !== null && $minPrice >= 0) {
    $sql .= " AND p.price >= :min_price";
    $params[':min_price'] = $minPrice;
}

if ($maxPrice !== false && $maxPrice !== null && $maxPrice > 0) {
    $sql .= " AND p.price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

if ($ageGroupId) {
    $selectedAge = null;
    foreach ($ageGroups as $ag) {
        if ((int)$ag['id'] === $ageGroupId) {
            $selectedAge = $ag;
            break;
        }
    }

    if ($selectedAge) {
        $sql .= " AND (p.min_age <= :ag_max AND p.max_age >= :ag_min)";
        $params[':ag_min'] = (int)$selectedAge['min_age'];
        $params[':ag_max'] = (int)$selectedAge['max_age'];
    }
}

$sql .= " ORDER BY {$orderBy}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$resultsCount = count($products);

if ($userId > 0 && $searchTerm !== '') {
    $historyModel->addSearch($userId, $searchTerm, $resultsCount);
}

$recentSearches = ($userId > 0) ? $historyModel->getRecentSearches($userId, 5) : [];

$pageTitle = 'Search & Explore';
$activePage = 'search';
require_once __DIR__ . '/../includes/header.php';

?>

<style>
.search-wrapper {
    background-color: #fcfbfe;
    padding: 35px 0 60px;
    min-height: 85vh;
}

.search-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    align-items: start;
}

@media (max-width: 991px) {
    .search-layout {
        grid-template-columns: 1fr;
    }
}


.recent-searches-box {
    background: #fff;
    border: 1px solid #efeef9;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.chips-list {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-chip {
    display: inline-flex;
    align-items: center;
    background: #f1f0fc;
    color: #6c5ce7;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.search-chip:hover {
    background: #6c5ce7;
    color: #ffffff;
}

.btn-clear-history {
    background: none;
    border: none;
    color: #e74c3c;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
}


.filter-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 24px;
    border: 1px solid #ebe8f7;
    box-shadow: 0 4px 20px rgba(108, 92, 231, 0.03);
}

.filter-group {
    margin-bottom: 22px;
}

.filter-group:last-child {
    margin-bottom: 0;
}

.filter-heading {
    font-size: 13px;
    font-weight: 700;
    color: #8c89a0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.filter-select, .filter-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e2ddf7;
    border-radius: 12px;
    font-size: 14px;
    color: #2d3436;
    background-color: #fff;
    outline: none;
    transition: border-color 0.2s;
}

.filter-select:focus, .filter-input:focus {
    border-color: #6c5ce7;
}

.btn-filter-apply {
    width: 100%;
    padding: 12px;
    background: #6c5ce7;
    color: #fff;
    border: none;
    border-radius: 14px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2);
    transition: background 0.2s;
}

.btn-filter-apply:hover {
    background: #5844e3;
}

.btn-reset {
    display: block;
    text-align: center;
    width: 100%;
    margin-top: 10px;
    color: #8c89a0;
    font-size: 13px;
    text-decoration: none;
}

.btn-reset:hover {
    color: #e74c3c;
}


.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.product-item {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #f0edf9;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(108, 92, 231, 0.08);
}

.product-img-wrap {
    width: 100%;
    height: 190px;
    background: #f6f5fc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.product-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.product-cat {
    font-size: 11px;
    font-weight: 700;
    color: #6c5ce7;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.product-title {
    font-size: 15px;
    font-weight: 700;
    color: #2d3436;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-footer {
    margin-top: auto;
    padding-top: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.product-price {
    font-size: 17px;
    font-weight: 800;
    color: #2d3436;
}

.btn-view-product {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1.5px solid #6c5ce7;
    background: transparent;
    color: #6c5ce7;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-view-product:hover {
    background: #6c5ce7;
    color: #fff;
}


.no-results {
    background: #fff;
    border-radius: 20px;
    padding: 50px 20px;
    text-align: center;
    border: 1px solid #ebe8f7;
}

.no-results h4 {
    color: #2d3436;
    margin-bottom: 8px;
    font-size: 18px;
}

.no-results p {
    color: #8c89a0;
    font-size: 14px;
    margin-bottom: 20px;
}
</style>

<div class="search-wrapper">
    <div class="container">

        <?php if (!empty($recentSearches)): ?>
            <div class="recent-searches-box">
                <div class="chips-list">
                    <span class="text-muted small fw-bold me-2">Recent Searches:</span>
                    <?php foreach ($recentSearches as $item): ?>
                        <a href="search.php?q=<?= urlencode($item['search_term']) ?>" class="search-chip">
                            <?= htmlspecialchars($item['search_term'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="btn-clear-history">Clear History</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="search-layout">
            <aside>
                <div class="filter-card">
                    <form method="GET" action="search.php" id="filterForm">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="filter-group">
                            <label class="filter-heading">Sort Products</label>
                            <select name="sort" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-heading">Category</label>
                            <select name="category" class="filter-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-heading">Age Group</label>
                            <select name="age_group" class="filter-select">
                                <option value="">All Ages</option>
                                <?php foreach ($ageGroups as $ag): ?>
                                    <option value="<?= $ag['id'] ?>" <?= $ageGroupId === (int)$ag['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ag['name'], ENT_QUOTES, 'UTF-8') ?> (<?= $ag['min_age'] ?>-<?= $ag['max_age'] ?> yrs)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-heading">Gender</label>
                            <select name="gender" class="filter-select">
                                <option value="">All</option>
                                <option value="boys" <?= $gender === 'boys' ? 'selected' : '' ?>>Boys</option>
                                <option value="girls" <?= $gender === 'girls' ? 'selected' : '' ?>>Girls</option>
                                <option value="unisex" <?= $gender === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-heading">Price Range (JOD)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="number" step="0.5" name="min_price" class="filter-input" placeholder="Min" value="<?= htmlspecialchars($_GET['min_price'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="number" step="0.5" name="max_price" class="filter-input" placeholder="Max" value="<?= htmlspecialchars($_GET['max_price'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn-filter-apply">Apply Filters</button>
                        <a href="search.php" class="btn-reset">Reset All Filters</a>
                    </form>
                </div>
            </aside>

            <main>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h5 style="margin: 0; font-weight: 700; color: #2d3436;">
                        <?= $searchTerm !== '' ? 'Search results for "' . htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') . '"' : 'Available Products' ?>
                    </h5>
                    <span style="color: #8c89a0; font-size: 13px; font-weight: 600;">
                        <?= $resultsCount ?> product<?= $resultsCount === 1 ? '' : 's' ?> found
                    </span>
                </div>

                <?php if (empty($products)): ?>
                    <div class="no-results">
                        <h4>No matching products found</h4>
                        <p>Try searching with different keywords or removing some filters.</p>
                        <a href="search.php" class="search-chip">Clear All Filters</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $prod): ?>
                            <div class="product-item">
                                <div class="product-img-wrap">
                                    <img src="<?= htmlspecialchars(!empty($prod['image']) ? $prod['image'] : 'assets/images/default.png', ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>"
                                         loading="lazy">
                                </div>
                                <div class="product-details">
                                    <span class="product-cat"><?= htmlspecialchars($prod['category_name'] ?? 'Toy', ENT_QUOTES, 'UTF-8') ?></span>
                                    <h6 class="product-title" title="<?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </h6>
                                    <div class="product-footer">
                                        <span class="product-price"><?= number_format((float)$prod['price'], 2) ?> JOD</span>
                                        <a href="product-details.php?id=<?= (int)$prod['id'] ?>" class="btn-view-product">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>

    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
