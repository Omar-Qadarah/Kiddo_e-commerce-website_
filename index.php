<?php
// Home (landing) page.
// 1) load data with the existing model classes
// 2) include the shared header (navbar)  -> includes/header.php
// 3) print the sections
// 4) include the shared footer           -> includes/footer.php
// Page files: assets/css/toys.css + assets/css/home.css, assets/js/home.js
// (listed in $pageStyles / $pageScripts below, linked by includes/header.php)

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/classes/Product.php';
require_once __DIR__ . '/classes/AgeGroup.php';
require_once __DIR__ . '/classes/Discount.php';

$productModel  = new Product($pdo);
$ageGroupModel = new AgeGroup($pdo);
$discountModel = new Discount($pdo);

$ageGroups      = $ageGroupModel->getAllWithToyCount();
$discountedToys = $productModel->getDiscountedToys(4);
$newArrivals    = $productModel->getNewArrivals(4);

// Hero line "Toys for ages X to Y" follows the age groups set by the admin.
$minAge = 3;
$maxAge = 12;
if (!empty($ageGroups)) {
    $minAge = min(array_column($ageGroups, 'min_age'));
    $maxAge = max(array_column($ageGroups, 'max_age'));
}

// The biggest discount running today feeds the first promo slide.
$today       = date('Y-m-d');
$topDiscount = null;
foreach ($discountModel->getAll() as $discount) {
    $started = empty($discount['starts_at']) || $discount['starts_at'] <= $today;
    $notOver = empty($discount['ends_at']) || $discount['ends_at'] >= $today;
    if ((int) $discount['is_active'] === 1 && $started && $notOver) {
        if ($topDiscount === null || (float) $discount['percentage'] > (float) $topDiscount['percentage']) {
            $topDiscount = $discount;
        }
    }
}

// Fallback picture for an age group that has no uploaded image.
$ageEmoji  = ['🧸', '🖍️', '🔬', '🤖', '🎨', '🚀'];
$ageColors = ['blue', 'green', 'orange', 'pink', 'purple'];

$pageTitle  = 'Home';
$activePage = 'home';
$pageStyles  = ['toys.css', 'home.css'];
$pageScripts = ['home.js'];
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ Hero ============ -->
<section class="hero">
    <span class="hero-bubble hero-bubble-1" aria-hidden="true"></span>
    <span class="hero-bubble hero-bubble-2" aria-hidden="true"></span>

    <div class="container hero-grid">
        <div class="hero-text">
            <p class="eyebrow eyebrow-yellow">Toys for ages <?= (int) $minAge ?> to <?= (int) $maxAge ?></p>
            <h1>Find toys that make your kids <span class="hl-yellow">smile!</span></h1>
            <p class="hero-lead">
                Kiddo's means <strong>happy play</strong>. We hand-pick toys by age and by what children
                love — art, puzzles, science, water fun and speedy cars — so choosing the perfect gift is easy.
            </p>
            <div class="hero-actions">
                <a class="btn btn-pink btn-lg" href="<?= BASE_URL ?>/products/index.php">Explore Now</a>
                <a class="btn btn-white btn-lg" href="#shop-by-age">Shop by age</a>
            </div>
        </div>

        <div class="hero-art">
            <img src="<?= BASE_URL ?>/assets/images/home/hero-toys.svg"
                 alt="A smiling teddy bear with balloons, a ball and toy blocks" width="520" height="440">
        </div>
    </div>
</section>

<!-- ============ Shop by age ============ -->
<?php if (!empty($ageGroups)): ?>
<section class="home-section" id="shop-by-age">
    <div class="container">
        <div class="section-head is-center">
            <p class="eyebrow">Shop by age</p>
            <h2>Which stage is your little one at?</h2>
            <p>Every stage is filled with toys that are safe, fun and just challenging enough.</p>
        </div>

        <div class="age-grid">
            <?php foreach ($ageGroups as $i => $group): ?>
                <?php $color = $ageColors[$i % count($ageColors)]; ?>
                <a class="age-card is-<?= $color ?>"
                   href="<?= BASE_URL ?>/products/index.php?age=<?= urlencode($group['slug']) ?>">
                    <span class="age-card-top">
                        <span class="age-pic">
                            <?php if (!empty($group['image'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars(ltrim($group['image'], '/')) ?>"
                                     alt="" loading="lazy" width="80" height="80">
                            <?php else: ?>
                                <span aria-hidden="true"><?= $ageEmoji[$i % count($ageEmoji)] ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="age-range"><?= (int) $group['min_age'] ?>–<?= (int) $group['max_age'] ?></span>
                    </span>
                    <span class="age-card-body">
                        <span class="age-name"><?= htmlspecialchars($group['name']) ?></span>
                        <span class="age-desc"><?= htmlspecialchars($group['description']) ?></span>
                        <span class="age-foot">
                            <span><?= (int) $group['toy_count'] ?> <?= (int) $group['toy_count'] === 1 ? 'toy' : 'toys' ?></span>
                            <span class="age-arrow" aria-hidden="true">›</span>
                        </span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ Promo slider ============ -->
<section class="home-section promo-section" aria-roledescription="carousel" aria-label="Offers">
    <div class="container">
        <div class="promo-slider" data-slider>
            <div class="promo-track">
                <article class="promo-slide is-orange" aria-label="Slide 1 of 4">
                    <div class="promo-text">
                        <span class="promo-tag">Limited time</span>
                        <?php if ($topDiscount): ?>
                            <h2><?= htmlspecialchars($topDiscount['title']) ?></h2>
                            <p>
                                Save up to <?= rtrim(rtrim(number_format((float) $topDiscount['percentage'], 2), '0'), '.') ?>%
                                <?= !empty($topDiscount['ends_at']) ? 'until ' . date('M j', strtotime($topDiscount['ends_at'])) : 'while it lasts' ?>.
                            </p>
                        <?php else: ?>
                            <h2>Happy prices every week</h2>
                            <p>New deals on favourite toys land every week — check back often.</p>
                        <?php endif; ?>
                        <a class="btn btn-white" href="#discounts">See deals</a>
                    </div>
                    <img class="promo-art" src="<?= BASE_URL ?>/assets/images/home/promo-gift.svg" alt="" width="200" height="180">
                </article>

                <article class="promo-slide is-pink" aria-label="Slide 2 of 4">
                    <div class="promo-text">
                        <span class="promo-tag">Fresh on the shelf</span>
                        <h2>New toys every week</h2>
                        <p>Hand-picked arrivals for every age, from first cuddles to big science kits.</p>
                        <a class="btn btn-white" href="#new-arrivals">See what's new</a>
                    </div>
                    <img class="promo-art" src="<?= BASE_URL ?>/assets/images/home/promo-new.svg" alt="" width="200" height="180">
                </article>

                <article class="promo-slide is-green" aria-label="Slide 3 of 4">
                    <div class="promo-text">
                        <span class="promo-tag">Easy payment</span>
                        <h2>Pay cash on delivery</h2>
                        <p>No cards needed. Pay when your order arrives at your door, anywhere in Jordan.</p>
                        <a class="btn btn-white" href="<?= BASE_URL ?>/products/index.php">Start shopping</a>
                    </div>
                    <img class="promo-art" src="<?= BASE_URL ?>/assets/images/home/promo-truck.svg" alt="" width="200" height="180">
                </article>

                <article class="promo-slide is-purple" aria-label="Slide 4 of 4">
                    <div class="promo-text">
                        <span class="promo-tag">Birthday surprise</span>
                        <h2>10% off on their big day</h2>
                        <p>Add the child's date of birth at checkout — if it's today, we take 10% off.</p>
                        <a class="btn btn-white" href="<?= BASE_URL ?>/products/index.php">Shop now</a>
                    </div>
                    <img class="promo-art" src="<?= BASE_URL ?>/assets/images/home/promo-cake.svg" alt="" width="200" height="180">
                </article>
            </div>
        </div>

        <div class="promo-controls">
            <button type="button" class="round-btn" data-slide-prev aria-label="Previous slide">‹</button>
            <div class="promo-dots" data-slide-dots></div>
            <button type="button" class="round-btn" data-slide-next aria-label="Next slide">›</button>
        </div>
    </div>
</section>

<!-- ============ Discounts ============ -->
<?php if (!empty($discountedToys)): ?>
<section class="home-section" id="discounts">
    <div class="container">
        <div class="section-head is-row">
            <div>
                <p class="eyebrow">Discounts</p>
                <h2>Happy prices this week</h2>
            </div>
            <a class="see-all" href="<?= BASE_URL ?>/products/index.php">See all toys →</a>
        </div>
        <div class="toy-grid">
            <?php foreach ($discountedToys as $toyIndex => $toy): ?>
                <?php require __DIR__ . '/includes/toy-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ New arrivals ============ -->
<section class="home-section" id="new-arrivals">
    <div class="container">
        <div class="section-head is-row">
            <div>
                <p class="eyebrow">Fresh on the shelf</p>
                <h2>New arrivals</h2>
            </div>
            <a class="see-all" href="<?= BASE_URL ?>/products/index.php">Browse everything →</a>
        </div>
        <?php if (!empty($newArrivals)): ?>
            <div class="toy-grid">
                <?php foreach ($newArrivals as $toyIndex => $toy): ?>
                    <?php $toyIndex += 2; // different tints from the discounts row ?>
                    <?php require __DIR__ . '/includes/toy-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-note">New toys are on their way — check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
