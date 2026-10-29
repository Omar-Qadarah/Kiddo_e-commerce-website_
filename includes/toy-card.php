<?php
// One toy card. Reused by every toy grid on the storefront.
// Expects $toy (a row from Product::getToyCards: getDiscountedToys,
// getNewArrivals, getFilteredToys) and optionally:
//   $toyIndex             picks a soft background tint
//   $cardImages           list of image URLs -> shows ‹ › arrows when 2 or more
//   $cardShowDescription  true -> short description under the name   (Toys page)
//   $cardShowPrice        false -> hide the price (default true: every page shows prices)
//
// The toy name is the link to products/details.php?id=<id>; CSS stretches it
// over the whole card, so clicking anywhere opens the toy, except the arrows,
// which sit above the link (assets/js/toys.js).
// Styles: assets/css/toys.css

$toyIndex     = $toyIndex ?? 0;
$showDesc     = $cardShowDescription ?? false;
$showPrice    = $cardShowPrice ?? true;
$price        = (float) $toy['price'];
$discountPct  = (float) $toy['discount_pct'];
$finalPrice   = $discountPct > 0 ? round($price * (1 - $discountPct / 100), 2) : $price;
$rating       = round((float) $toy['avg_rating'], 1);
$reviewCount  = (int) $toy['review_count'];
$images       = $cardImages ?? [];
if (empty($images)) {
    $images = [!empty($toy['image'])
        ? BASE_URL . '/' . ltrim($toy['image'], '/')
        : BASE_URL . '/assets/images/home/toy-placeholder.svg'];
}
$detailsUrl   = BASE_URL . '/products/details.php?id=' . (int) $toy['id'];
$tints        = ['tint-yellow', 'tint-pink', 'tint-purple', 'tint-mint', 'tint-blue'];
?>
<article class="toy-card">
    <div class="toy-card-media <?= $tints[$toyIndex % count($tints)] ?>"
        <?php if (count($images) > 1): ?>
            data-card-gallery data-images="<?= htmlspecialchars(json_encode(array_values($images))) ?>"
        <?php endif; ?>>
        <?php if ($discountPct > 0 && $showPrice): ?>
            <span class="toy-badge">-<?= rtrim(rtrim(number_format($discountPct, 2), '0'), '.') ?>%</span>
        <?php endif; ?>

        <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($toy['name']) ?>"
             loading="lazy" width="220" height="220">

        <?php if (count($images) > 1): ?>
            <button type="button" class="card-arrow is-prev" data-card-step="-1"
                    aria-label="Previous image of <?= htmlspecialchars($toy['name']) ?>">‹</button>
            <button type="button" class="card-arrow is-next" data-card-step="1"
                    aria-label="Next image of <?= htmlspecialchars($toy['name']) ?>">›</button>
            <span class="card-dots" aria-hidden="true">
                <?php foreach ($images as $i => $unused): ?>
                    <span class="<?= $i === 0 ? 'is-on' : '' ?>"></span>
                <?php endforeach; ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="toy-card-body">
        <h3 class="toy-card-name">
            <a class="toy-card-link" href="<?= $detailsUrl ?>"><?= htmlspecialchars($toy['name']) ?></a>
        </h3>

        <?php if ($showDesc && trim((string) $toy['description']) !== ''): ?>
            <p class="toy-card-desc"><?= htmlspecialchars($toy['description']) ?></p>
        <?php endif; ?>

        <span class="toy-rating">
            <span class="stars" style="--rating: <?= $rating ?>" role="img"
                  aria-label="Rated <?= $rating ?> out of 5"></span>
            <span class="toy-rating-text">
                <?= $reviewCount > 0 ? $rating . ' (' . $reviewCount . ')' : 'No reviews yet' ?>
            </span>
        </span>

        <?php if ($showPrice): ?>
            <span class="toy-price">
                <strong><?= number_format($finalPrice, 2) ?> JOD</strong>
                <?php if ($discountPct > 0): ?>
                    <del><?= number_format($price, 2) ?> JOD</del>
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </div>
</article>
