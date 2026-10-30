<?php
// Toy details page: products/details.php?id=<toy id>
// Shows the toy's images, name, description, "How to play", price,
// quantity − / + with "Add to cart" (posts to cart/add.php), and customer reviews.
// Logged-in customers can write a review (posts to products/add_review.php);
// visitors see the reviews and a "Log in to write a review" button.
// Page files: assets/css/toy-details.css (+ storefront.css), assets/js/storefront.js
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/shop_functions.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../classes/ProductImage.php';
require_once __DIR__ . '/../classes/Review.php';

$catalog    = new ToyCatalog($pdo);
$imageModel = new ProductImage($pdo);

$toyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$toy   = ($toyId && $toyId > 0) ? $catalog->getToy($toyId) : false;

if (!$toy) {
    http_response_code(404);
    $pageTitle  = 'Toy not found';
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

// Images: products.image first, then the images table (reuses ProductImage).
$images = shopToyImages($toy, array_column($imageModel->getByProductId((int) $toy['id']), 'image_path'));

$reviews     = $catalog->getReviews((int) $toy['id']);
$price       = (float) $toy['price'];
$finalPrice  = shopFinalPrice($toy);
$hasDiscount = $finalPrice < $price;
$rating      = round((float) $toy['avg_rating'], 1);
$reviewCount = (int) $toy['review_count'];
$stock       = (int) $toy['stock'];
$tints       = ['tint-yellow', 'tint-purple', 'tint-peach', 'tint-pink', 'tint-mint', 'tint-blue'];
$tint        = $tints[(int) $toy['category_id'] % count($tints)];

// Message from cart/add.php, shown once
$cartFlash = $_SESSION['cart_flash'] ?? null;
unset($_SESSION['cart_flash']);

// ---------- Write a review ----------
// Same login check as the navbar: auth/login.php puts the user id in the session.
$reviewUserId    = (int) ($_SESSION['user_id'] ?? 0);
$isLoggedIn      = $reviewUserId > 0;
$alreadyReviewed = $isLoggedIn && (new Review($pdo))->hasReviewed($reviewUserId, (int) $toy['id']);

// Message from products/add_review.php, shown once (+ what the user typed on an error)
$reviewFlash = $_SESSION['review_flash'] ?? null;
unset($_SESSION['review_flash']);
$oldRating  = (string) ($reviewFlash['old']['rating'] ?? '');
$oldComment = (string) ($reviewFlash['old']['comment'] ?? '');

$pageTitle  = $toy['name'];
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
        <!-- ============ Images ============ -->
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

        <!-- ============ Info ============ -->
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
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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
            
            <?php if ($cartFlash): ?>
                <p class="details-flash is-<?= $cartFlash['type'] ?>" role="status">
                    <?= htmlspecialchars($cartFlash['message']) ?>
                    <?php if ($cartFlash['type'] === 'success'): ?>
                        <a href="<?= BASE_URL ?>/cart/index.php">View cart</a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ Customer reviews ============ -->
<section class="container details-reviews" id="reviews">
    <h2>What families say</h2>

    <?php if ($reviewFlash): ?>
        <p class="details-flash is-<?= $reviewFlash['type'] === 'success' ? 'success' : 'error' ?>" role="status">
            <?= htmlspecialchars($reviewFlash['message']) ?>
        </p>
    <?php endif; ?>

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

    <!-- ============ Write a review ============ -->
    <div class="review-form-box">
        <?php if (!$isLoggedIn): ?>
            <div class="review-login">
                <p><strong>Have this toy?</strong> Log in to share your rating and comment with other families.</p>
                <a class="btn btn-primary" href="<?= BASE_URL ?>/products/add_review.php?id=<?= (int) $toy['id'] ?>">Log in to write a review</a>
            </div>
        <?php elseif ($alreadyReviewed): ?>
            <p class="review-done">✓ You have already reviewed this toy. Thank you for helping other families!</p>
        <?php else: ?>
            <form class="review-form" action="<?= BASE_URL ?>/products/add_review.php" method="post">
                <h3>Write a review</h3>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $toy['id'] ?>">

                <fieldset class="star-input">
                    <legend>Your rating</legend>
                    <!-- 5 → 1 in the HTML so the CSS can colour the stars before the checked one -->
                    <div class="star-row">
                    <?php for ($star = 5; $star >= 1; $star--): ?>
                        <input type="radio" id="star-<?= $star ?>" name="rating" value="<?= $star ?>"
                               <?= $oldRating === (string) $star ? 'checked' : '' ?> required>
                        <label for="star-<?= $star ?>" title="<?= $star ?> <?= $star === 1 ? 'star' : 'stars' ?>">★<span class="sr-only"><?= $star ?> <?= $star === 1 ? 'star' : 'stars' ?></span></label>
                    <?php endfor; ?>
                    </div>
                </fieldset>

                <label class="review-label" for="review-comment">Your comment</label>
                <textarea id="review-comment" name="comment" rows="4" minlength="3" maxlength="500" required
                          placeholder="What did your child like about this toy?"><?= htmlspecialchars($oldComment) ?></textarea>
                <small class="review-hint">3–500 characters.</small>

                <button class="btn btn-primary" type="submit">Post review</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
