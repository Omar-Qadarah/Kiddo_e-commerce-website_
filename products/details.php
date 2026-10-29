<?php


require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductImage.php';
require_once __DIR__ . '/../classes/Review.php';

$productModel = new Product($pdo);
$imageModel   = new ProductImage($pdo);
$reviewModel  = new Review($pdo);

$id  = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$toy = $id ? $productModel->getToyDetails($id) : false;

if (!$toy) {
    http_response_code(404);
    $pageTitle   = 'Toy not found';
    $activePage  = 'toys';
    $pageStyles  = ['toys.css', 'product.css'];
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="container pd-missing">
        <h1>Oops! This toy is not on the shelf.</h1>
        <p>It may have been sold out or removed. Let's find another one.</p>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php">Back to home</a>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$price       = (float) $toy['price'];
$discountPct = (float) $toy['discount_pct'];
$finalPrice  = $discountPct > 0 ? round($price * (1 - $discountPct / 100), 2) : $price;
$discountTxt = rtrim(rtrim(number_format($discountPct, 2), '0'), '.');
$stock       = (int) $toy['stock'];


$gallery = [];
$addImage = function (?string $path, ?string $alt) use (&$gallery, $toy) {
    $path = ltrim(trim((string) $path), '/');
    if ($path !== '' && !isset($gallery[$path])) {
        $gallery[$path] = [
            'src' => BASE_URL . '/' . $path,
            'alt' => $alt ?: $toy['name'],
        ];
    }
};
$addImage($toy['image'], $toy['name']);
foreach ($imageModel->getByProductId((int) $toy['id']) as $image) {
    $addImage($image['image_path'], $image['alt_text']);
}
if (empty($gallery)) {
    $gallery['placeholder'] = [
        'src' => BASE_URL . '/assets/images/home/toy-placeholder.svg',
        'alt' => $toy['name'],
    ];
}
$gallery   = array_values($gallery);
$mainImage = $gallery[0];

$reviews     = $reviewModel->getVisibleForProducts([(int) $toy['id']], 100)[(int) $toy['id']] ?? [];
$rating      = round((float) $toy['avg_rating'], 1);
$reviewCount = (int) $toy['review_count'];
$ratingBars  = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
foreach ($reviews as $review) {
    $ratingBars[$review['rating']]++;
}

$flash = $_SESSION['cart_flash'] ?? null;
unset($_SESSION['cart_flash']);

$pageTitle   = $toy['name'];
$activePage  = 'toys';
$pageStyles  = ['toys.css', 'product.css'];
$pageScripts = ['product.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="container pd">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <span aria-hidden="true">/</span>
        <a href="<?= BASE_URL ?>/products/index.php">Toys</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page"><?= htmlspecialchars($toy['name']) ?></span>
    </nav>

    <?php if ($flash): ?>
        <div class="pd-flash is-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="status">
            <span><?= htmlspecialchars($flash['message']) ?></span>
            <?php if ($flash['type'] === 'success'): ?>
                <a href="<?= BASE_URL ?>/cart/index.php">View cart →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="pd-grid">
        <!-- ===== A. Images ===== -->
        <div class="pd-gallery" data-gallery>
            <div class="pd-main">
                <?php if ($discountPct > 0): ?>
                    <span class="toy-badge">-<?= $discountTxt ?>%</span>
                <?php endif; ?>
                <img id="pd-main-image" src="<?= htmlspecialchars($mainImage['src']) ?>"
                     alt="<?= htmlspecialchars($mainImage['alt']) ?>" width="520" height="520">
            </div>

            <?php if (count($gallery) > 1): ?>
                <div class="pd-thumbs" role="list">
                    <?php foreach ($gallery as $i => $image): ?>
                        <button type="button" class="pd-thumb" role="listitem"
                                data-src="<?= htmlspecialchars($image['src']) ?>"
                                data-alt="<?= htmlspecialchars($image['alt']) ?>"
                                aria-label="Show image <?= $i + 1 ?> of <?= count($gallery) ?>"
                                aria-current="<?= $i === 0 ? 'true' : 'false' ?>">
                            <img src="<?= htmlspecialchars($image['src']) ?>" alt="" width="90" height="90" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="pd-info">
            <p class="pd-meta">
                <?= htmlspecialchars($toy['category_name']) ?>
                <?php if (!empty($toy['gender'])): ?> · <?= htmlspecialchars(ucfirst($toy['gender'])) ?><?php endif; ?>
            </p>
            <h1><?= htmlspecialchars($toy['name']) ?></h1>

            <a class="toy-rating pd-rating" href="#reviews">
                <span class="stars" style="--rating: <?= $rating ?>" role="img" aria-label="Rated <?= $rating ?> out of 5"></span>
                <span class="toy-rating-text">
                    <?= $reviewCount > 0
                        ? $rating . ' out of 5 · ' . $reviewCount . ($reviewCount === 1 ? ' review' : ' reviews')
                        : 'No reviews yet' ?>
                </span>
            </a>

            <div class="toy-price pd-price">
                <strong><?= number_format($finalPrice, 2) ?> JOD</strong>
                <?php if ($discountPct > 0): ?>
                    <del><?= number_format($price, 2) ?> JOD</del>
                    <span class="pd-save">You save <?= number_format($price - $finalPrice, 2) ?> JOD</span>
                <?php endif; ?>
            </div>

            <div class="pd-chips">
                <?php if ((int) $toy['max_age'] > 0): ?>
                    <span class="pd-chip">Ages <?= (int) $toy['min_age'] ?>–<?= (int) $toy['max_age'] ?></span>
                <?php endif; ?>
                <span class="pd-chip <?= $stock > 0 ? 'is-in' : 'is-out' ?>">
                    <?= $stock > 0 ? $stock . ' in stock' : 'Out of stock' ?>
                </span>
            </div>

            <div class="pd-description">
                <h2>About this toy</h2>
                <p><?= nl2br(htmlspecialchars($toy['description'] ?: 'No description yet.')) ?></p>
                <?php if (trim((string) $toy['instructions']) !== ''): ?>
                    <p class="pd-howto"><strong>How to play:</strong> <?= htmlspecialchars($toy['instructions']) ?></p>
                <?php endif; ?>
            </div>

            <form class="pd-cart" action="<?= BASE_URL ?>/cart/add.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $toy['id'] ?>">

                <div class="qty" data-qty>
                    <button type="button" class="qty-btn" data-step="-1" aria-label="Decrease quantity">−</button>
                    <label class="sr-only" for="quantity">Quantity</label>
                    <input id="quantity" class="qty-input" type="number" name="quantity" value="1"
                           min="1" max="<?= max(1, $stock) ?>" step="1" inputmode="numeric"
                           <?= $stock > 0 ? '' : 'disabled' ?>>
                    <button type="button" class="qty-btn" data-step="1" aria-label="Increase quantity">+</button>
                </div>

                <button class="btn btn-primary btn-lg pd-add" type="submit" <?= $stock > 0 ? '' : 'disabled' ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.4L21 8H6.2"/>
                        <circle cx="10" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
                    </svg>
                    <?= $stock > 0 ? 'Add to Cart' : 'Out of stock' ?>
                </button>
            </form>

            <p class="pd-note">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/></svg>
                Cash on delivery — pay when your order arrives.
            </p>
        </div>
    </div>
</section>

<section class="container pd-reviews" id="reviews">
    <div class="section-head">
        <p class="eyebrow">Customer reviews</p>
        <h2>What families say</h2>
    </div>

    <?php if ($reviewCount > 0): ?>
        <div class="reviews-grid">
            <aside class="review-summary">
                <strong class="review-big"><?= number_format($rating, 1) ?></strong>
                <span class="stars" style="--rating: <?= $rating ?>" role="img" aria-label="Rated <?= $rating ?> out of 5"></span>
                <span class="review-total">Based on <?= $reviewCount ?> <?= $reviewCount === 1 ? 'review' : 'reviews' ?></span>

                <ul class="review-bars">
                    <?php foreach ($ratingBars as $stars => $count): ?>
                        <li>
                            <span><?= $stars ?> ★</span>
                            <span class="bar"><span style="width: <?= count($reviews) ? round($count / count($reviews) * 100) : 0 ?>%"></span></span>
                            <span><?= $count ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <ul class="review-list">
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $parts    = preg_split('/\s+/', $review['name'], -1, PREG_SPLIT_NO_EMPTY);
                    $initials = '';
                    foreach (array_slice($parts, 0, 2) as $part) {
                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                    }
                    ?>
                    <li class="review-card">
                        <div class="review-head">
                            <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                            <span>
                                <strong><?= htmlspecialchars($review['name']) ?></strong>
                                <small><?= htmlspecialchars($review['date']) ?></small>
                            </span>
                            <span class="stars" style="--rating: <?= (int) $review['rating'] ?>" role="img"
                                  aria-label="Rated <?= (int) $review['rating'] ?> out of 5"></span>
                        </div>
                        <?php if (trim($review['comment']) !== ''): ?>
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p class="empty-note">No reviews yet — be the first to share what your child thinks!</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
