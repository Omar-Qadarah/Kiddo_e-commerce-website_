<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/shop_functions.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../classes/ProductImage.php';
require_once __DIR__ . '/../classes/Recommendation.php';

$catalog = new ToyCatalog($pdo);
$imageModel = new ProductImage($pdo);
$recModel = new Recommendation($pdo);

$toyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$toy = ($toyId && $toyId > 0) ? $catalog->getToy($toyId) : false;

if (!$toy) {
    http_response_code(404);
    $pageTitle = 'Toy not found';
    $activePage = 'toys';
    $pageStyles = ['toy-details.css'];
    require_once __DIR__ . '/../includes/shop_header.php';
    ?>
    <section class="container details-missing">
        <span aria-hidden="true">🧸</span>
        <h1>We couldn't find this toy</h1>
        <p>It may have been removed or the link is wrong.</p>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/products/index.php">Back to all toys</a>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$images = shopToyImages($toy, array_column($imageModel->getByProductId((int) $toy['id']), 'image_path'));

$reviews = $catalog->getReviews((int) $toy['id']);
$price = (float) $toy['price'];
$finalPrice = shopFinalPrice($toy);
$hasDiscount = $finalPrice < $price;
$rating = round((float) $toy['avg_rating'], 1);
$reviewCount = (int) $toy['review_count'];
$stock = (int) $toy['stock'];
$tints = ['tint-yellow', 'tint-purple', 'tint-peach', 'tint-pink', 'tint-mint', 'tint-blue'];
$tint = $tints[(int) $toy['category_id'] % count($tints)];

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$recommendedToys = $recModel->getRecommendations($userId, [(int) $toy['id']], 4);

$pageTitle = $toy['name'];
$activePage = 'toys';
$pageStyles = ['toy-details.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>

<section class="container details">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <span aria-hidden="true">/</span>
        <a href="<?= BASE_URL ?>/products/index.php?category=<?= urlencode($toy['category_slug']) ?>"><?= htmlspecialchars($toy['category_name']) ?></a>
        <span aria-hidden="true">/</span>
        <span aria-current="page"><?= htmlspecialchars($toy['name']) ?></span>
    </nav>

    <div class="details-grid">
        <div class="details-gallery" data-gallery>
            <div class="details-main-image <?= $tint ?>">
                <?php if ($hasDiscount): ?>
                    <span class="toy-badge">-<?= rtrim(rtrim(number_format((float) $toy['discount_pct'], 2), '0'), '.') ?>%</span>
                <?php endif; ?>
                <span class="details-circle">
                    <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($toy['name']) ?>" data-gallery-main>
                </span>
            </div>

            <?php if (count($images) > 1): ?>
                <div class="details-thumbs">
                    <?php foreach ($images as $i => $url): ?>
                        <button type="button" class="details-thumb <?= $tint ?> <?= $i === 0 ? 'is-active' : '' ?>"
                                data-gallery-thumb="<?= htmlspecialchars($url) ?>"
                                aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
                                aria-label="Show picture <?= $i + 1 ?>">
                            <img src="<?= htmlspecialchars($url) ?>" alt="" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="details-info">
            <span class="toy-card-cat"><?= htmlspecialchars($toy['category_name']) ?></span>
            <h1><?= htmlspecialchars($toy['name']) ?></h1>

            <span class="toy-rating">
                <?= shopStars($rating) ?>
                <span class="toy-rating-text">
                    <?= $reviewCount > 0
                        ? $rating . ' (' . $reviewCount . ' ' . ($reviewCount === 1 ? 'review' : 'reviews') . ')'
                        : 'Be the first to review' ?>
                </span>
            </span>

            <div class="details-chips">
                <span class="chip">Ages <?= (int) $toy['min_age'] ?>–<?= (int) $toy['max_age'] ?></span>
                <span class="chip"><?= shopGenderLabel((string) $toy['gender']) ?></span>
                <span class="chip <?= $stock > 0 ? 'chip-green' : 'chip-pink' ?>">
                    <?= $stock > 0 ? $stock . ' in stock' : 'Out of stock' ?>
                </span>
            </div>

            <div class="details-price">
                <strong><?= shopMoney($finalPrice) ?></strong>
                <?php if ($hasDiscount): ?>
                    <del><?= shopMoney($price) ?></del>
                <?php endif; ?>
            </div>

            <p class="details-desc"><?= nl2br(htmlspecialchars($toy['description'])) ?></p>

            <div class="how-to-play">
                <h2>How to play</h2>
                <p>
                    <?= trim((string) $toy['instructions']) !== ''
                        ? nl2br(htmlspecialchars($toy['instructions']))
                        : 'Instructions for this toy are coming soon.' ?>
                </p>
            </div>

            <form class="details-buy" action="<?= BASE_URL ?>/cart/add.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="product_id" value="<?= (int) $toy['id'] ?>">

                <div class="qty" data-qty>
                    <button type="button" class="qty-btn" data-qty-minus aria-label="Decrease quantity" <?= $stock > 0 ? '' : 'disabled' ?>>−</button>
                    <label class="sr-only" for="qty-input">Quantity</label>
                    <input id="qty-input" type="number" name="quantity" value="1" min="1"
                           max="<?= max(1, $stock) ?>" inputmode="numeric" <?= $stock > 0 ? '' : 'disabled' ?>>
                    <button type="button" class="qty-btn" data-qty-plus aria-label="Increase quantity" <?= $stock > 0 ? '' : 'disabled' ?>>+</button>
                </div>

                <button class="btn btn-primary details-add" type="submit" <?= $stock > 0 ? '' : 'disabled' ?>>
                    <?= $stock > 0 ? 'Add to cart' : 'Out of stock' ?>
                </button>
            </form>
        </div>
    </div>
</section>

<?php if (!empty($recommendedToys)): ?>
<section class="container details-recommendations" style="margin-top: 48px; margin-bottom: 24px;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #2d3436; margin-bottom: 4px;">You May Also Love</h2>
        <p style="color: #636e72; font-size: 0.95rem; margin: 0;">Frequently paired toys and top recommendations for you</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
        <?php foreach ($recommendedToys as $recToy): 
            $recPrice = (float) $recToy['price'];
            $recImg = !empty($recToy['image']) ? BASE_URL . '/uploads/products/' . basename($recToy['image']) : BASE_URL . '/assets/images/toy-placeholder.svg';
        ?>
            <div style="background: #ffffff; border: 1px solid #edf0f5; border-radius: 18px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 14px rgba(0,0,0,0.02); transition: transform 0.2s ease;">
                <div>
                    <a href="<?= BASE_URL ?>/products/details.php?id=<?= (int) $recToy['id'] ?>" style="text-decoration: none; display: block;">
                        <div style="background: #fafbfc; border-radius: 12px; height: 160px; display: flex; align-items: center; justify-content: center; padding: 12px; margin-bottom: 12px; border: 1px solid #f1f2f6;">
                            <img src="<?= htmlspecialchars($recImg) ?>" alt="<?= htmlspecialchars($recToy['name']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" onerror="this.src='<?= BASE_URL ?>/assets/images/toy-placeholder.svg';">
                        </div>
                        <span style="display: inline-block; font-size: 0.75rem; font-weight: 700; color: #6c5ce7; background: #ede9fe; padding: 3px 10px; border-radius: 12px; margin-bottom: 6px;">
                            <?= htmlspecialchars($recToy['category_name'] ?? 'Toy') ?>
                        </span>
                        <h3 style="font-size: 0.95rem; font-weight: 700; color: #2d3436; margin: 0 0 8px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($recToy['name']) ?>">
                            <?= htmlspecialchars($recToy['name']) ?>
                        </h3>
                    </a>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px solid #f8f9fa;">
                    <strong style="color: #2d3436; font-size: 1.05rem;"><?= shopMoney($recPrice) ?></strong>
                    <form action="<?= BASE_URL ?>/cart/add.php" method="post" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="product_id" value="<?= (int) $recToy['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 10px; height: auto;" title="Add to cart">
                            + Add
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="container details-reviews">
    <h2>What families say</h2>

    <?php if (empty($reviews)): ?>
        <p class="reviews-empty">No reviews yet — be the first to tell other parents what you think!</p>
    <?php else: ?>
        <ul class="review-list">
            <?php foreach ($reviews as $review): ?>
                <li class="review">
                    <div class="review-head">
                        <span class="review-avatar" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($review['first_name'], 0, 1))) ?></span>
                        <div>
                            <strong><?= htmlspecialchars($review['first_name']) ?></strong>
                            <span class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></span>
                        </div>
                        <?= shopStars((float) $review['rating']) ?>
                    </div>
                    <?php if (trim((string) $review['comment']) !== ''): ?>
                        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>