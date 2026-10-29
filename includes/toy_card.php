<?php
// One toy card, used by the landing page and the toys page.
// Expects:
//   $toy         a row from ToyCatalog (getDiscountedToys, getNewArrivals, getFilteredToys ...)
//   $cardImages  (optional) list of image URLs; 2 or more -> ‹ › arrows on the image
//   $cardMode    'landing' -> image, name, description, rating, price only
//                'shop'    -> + category · gender, stock and "Add to cart" (toys page)
// "How to play" is never shown on cards; it lives on products/details.php.
// The name link is stretched over the whole card (CSS), so a click anywhere
// opens the details page; the arrows and the cart button sit above it.

$cardMode     = $cardMode ?? 'landing';
$isShopCard   = $cardMode === 'shop';
$images       = !empty($cardImages) ? array_values($cardImages) : shopToyImages($toy);
$price        = (float) $toy['price'];
$finalPrice   = shopFinalPrice($toy);
$hasDiscount  = $finalPrice < $price;
$rating       = round((float) $toy['avg_rating'], 1);
$reviewCount  = (int) $toy['review_count'];
$stock        = (int) $toy['stock'];
$detailsUrl   = BASE_URL . '/products/details.php?id=' . (int) $toy['id'];
$tints        = ['tint-yellow', 'tint-purple', 'tint-peach', 'tint-pink', 'tint-mint', 'tint-blue'];
$tint         = $tints[(int) $toy['category_id'] % count($tints)];
?>
<article class="toy-card">
    <div class="toy-card-media <?= $tint ?>" <?= count($images) > 1 ? 'data-card-gallery' : '' ?>>
        <?php if ($isShopCard && $hasDiscount): ?>
            <span class="toy-badge">-<?= rtrim(rtrim(number_format((float) $toy['discount_pct'], 2), '0'), '.') ?>%</span>
        <?php endif; ?>

        <span class="toy-card-circle">
            <?php foreach ($images as $i => $url): ?>
                <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($toy['name']) ?>"
                     class="<?= $i === 0 ? 'is-shown' : '' ?>" loading="lazy" width="220" height="220">
            <?php endforeach; ?>
        </span>

        <?php if ($isShopCard): ?>
            <span class="toy-age"><?= (int) $toy['min_age'] ?>–<?= (int) $toy['max_age'] ?> yrs</span>
        <?php endif; ?>

        <?php if (count($images) > 1): ?>
            <button type="button" class="card-arrow is-prev" data-card-step="-1"
                    aria-label="Previous image of <?= htmlspecialchars($toy['name']) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button type="button" class="card-arrow is-next" data-card-step="1"
                    aria-label="Next image of <?= htmlspecialchars($toy['name']) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <span class="card-dots" aria-hidden="true">
                <?php foreach ($images as $i => $unused): ?>
                    <span class="<?= $i === 0 ? 'is-on' : '' ?>"></span>
                <?php endforeach; ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="toy-card-body">
        <?php if ($isShopCard): ?>
            <span class="toy-card-cat">
                <?= htmlspecialchars($toy['category_name']) ?> · <?= shopGenderLabel((string) $toy['gender']) ?>
            </span>
        <?php endif; ?>

        <h3 class="toy-card-name">
            <a class="toy-card-link" href="<?= $detailsUrl ?>"><?= htmlspecialchars($toy['name']) ?></a>
        </h3>

        <?php if (trim((string) $toy['description']) !== ''): ?>
            <p class="toy-card-desc"><?= htmlspecialchars($toy['description']) ?></p>
        <?php endif; ?>

        <span class="toy-rating">
            <?= shopStars($rating) ?>
            <span class="toy-rating-text"><?= $reviewCount > 0 ? $rating . ' (' . $reviewCount . ')' : 'No reviews yet' ?></span>
        </span>

        <div class="toy-card-foot">
            <span class="toy-price">
                <strong><?= shopMoney($finalPrice) ?></strong>
                <?php if ($hasDiscount): ?>
                    <del><?= shopMoney($price) ?></del>
                <?php endif; ?>
            </span>
            <?php if ($isShopCard): ?>
                <span class="toy-stock <?= $stock > 0 ? '' : 'is-out' ?>">
                    <?= $stock > 0 ? $stock . ' in stock' : 'Out of stock' ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($isShopCard): ?>
            <form class="toy-card-cart" action="<?= BASE_URL ?>/cart/add.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $toy['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button class="btn btn-primary btn-block" type="submit" <?= $stock > 0 ? '' : 'disabled' ?>>
                    <?= $stock > 0 ? 'Add to cart' : 'Out of stock' ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</article>
