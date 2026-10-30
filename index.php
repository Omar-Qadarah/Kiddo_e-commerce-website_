<?php
// Landing page (index.php)
// Sections: hero, shop by age, promo slider, discounts, new arrivals,
// then the existing footer (includes/footer.php).
// Toy cards here show only: image, name, description, rating and price.
// Clicking a toy opens products/details.php?id=<id>.
// Page files: assets/css/landing.css (+ storefront.css), assets/js/storefront.js
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/classes/ToyCatalog.php';

$catalog     = new ToyCatalog($pdo);
$ageGroups   = $catalog->getAgeGroupsWithToyCount();
$discounted  = $catalog->getDiscountedToys(4);
$newArrivals = $catalog->getNewArrivals(4);

// "Toys for ages 3 to 12" — taken from the age groups the admin saved.
$minAge = $ageGroups ? min(array_map('intval', array_column($ageGroups, 'min_age'))) : 3;
$maxAge = $ageGroups ? max(array_map('intval', array_column($ageGroups, 'max_age'))) : 12;

// Look of each age card, in order (used when the age group has no picture).
$ageLooks = [
    ['color' => 'blue',   'emoji' => '🧸'],
    ['color' => 'green',  'emoji' => '🖍️'],
    ['color' => 'orange', 'emoji' => '🔬'],
    ['color' => 'pink',   'emoji' => '🤖'],
    ['color' => 'purple', 'emoji' => '🎨'],
];

$pageTitle  = 'Home';
$activePage = 'home';
$pageStyles = ['landing.css'];
require_once __DIR__ . '/includes/shop_header.php';
?>

<!-- ============ Hero ============ -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-text">
            <span class="hero-eyebrow">Toys for ages <?= $minAge ?> to <?= $maxAge ?></span>
            <h1>Find toys that make your kids <span>smile!</span></h1>
            <p>
                Kiddo's hand-picks toys by age and by what children <strong>love</strong> — art, puzzles,
                science, water fun and speedy cars — so choosing the perfect gift is easy.
            </p>
            <div class="hero-actions">
                <a class="btn btn-pink" href="<?= BASE_URL ?>/products/index.php">Explore Now</a>
                <a class="btn hero-btn-light" href="#shop-by-age">Shop by age</a>
            </div>
        </div>

        <div class="hero-art" aria-hidden="true">
            <svg viewBox="0 0 460 420" width="460" height="420">
                <circle cx="250" cy="215" r="180" fill="#8F75F7"/>
                <!-- paper plane -->
                <path d="M392 52 L330 96 L352 104 Z" fill="#fff"/>
                <path d="M392 52 L352 104 L356 128 Z" fill="#E4DCFF"/>
                <path d="M330 96 C300 110 285 118 268 132" stroke="#fff" stroke-width="2.5" stroke-dasharray="5 7" fill="none"/>
                <!-- balloons -->
                <path d="M152 112 C160 170 170 210 192 252" stroke="#fff" stroke-width="2.5" fill="none"/>
                <path d="M110 150 C118 200 140 230 180 262" stroke="#fff" stroke-width="2.5" fill="none"/>
                <ellipse cx="150" cy="82" rx="34" ry="42" fill="#FF5C93"/>
                <ellipse cx="140" cy="70" rx="8" ry="12" fill="#FF9EC0"/>
                <ellipse cx="108" cy="124" rx="30" ry="37" fill="#FFC93D"/>
                <ellipse cx="99" cy="113" rx="7" ry="10" fill="#FFE29A"/>
                <!-- stars -->
                <path d="M392 175 l7 14 15 2 -11 10 3 15 -14 -7 -14 7 3 -15 -11 -10 15 -2z" fill="#FFC93D"/>
                <path d="M70 262 l5 10 11 1 -8 7 2 11 -10 -5 -10 5 2 -11 -8 -7 11 -1z" fill="#fff" opacity=".9"/>
                <!-- teddy -->
                <circle cx="215" cy="122" r="30" fill="#B97A45"/>
                <circle cx="215" cy="122" r="17" fill="#F2C9A0"/>
                <circle cx="345" cy="122" r="30" fill="#B97A45"/>
                <circle cx="345" cy="122" r="17" fill="#F2C9A0"/>
                <ellipse cx="280" cy="300" rx="92" ry="92" fill="#C98A52"/>
                <ellipse cx="280" cy="312" rx="56" ry="62" fill="#F2C9A0"/>
                <circle cx="280" cy="180" r="92" fill="#C98A52"/>
                <ellipse cx="280" cy="208" rx="44" ry="34" fill="#F2C9A0"/>
                <circle cx="248" cy="166" r="9" fill="#2A2150"/>
                <circle cx="312" cy="166" r="9" fill="#2A2150"/>
                <circle cx="251" cy="163" r="3" fill="#fff"/>
                <circle cx="315" cy="163" r="3" fill="#fff"/>
                <ellipse cx="280" cy="196" rx="13" ry="10" fill="#2A2150"/>
                <path d="M266 216 Q280 230 294 216" stroke="#2A2150" stroke-width="4" stroke-linecap="round" fill="none"/>
                <circle cx="230" cy="200" r="11" fill="#FF8FA8" opacity=".7"/>
                <circle cx="330" cy="200" r="11" fill="#FF8FA8" opacity=".7"/>
                <path d="M258 258 L280 268 L258 278 Z M302 258 L280 268 L302 278 Z" fill="#E8457C"/>
                <circle cx="280" cy="268" r="7" fill="#D6336B"/>
                <ellipse cx="200" cy="330" rx="26" ry="20" fill="#B97A45"/>
                <ellipse cx="360" cy="330" rx="26" ry="20" fill="#B97A45"/>
                <!-- ball -->
                <circle cx="168" cy="352" r="40" fill="#FFC93D"/>
                <path d="M148 318 C178 340 178 368 152 388" stroke="#E8457C" stroke-width="9" fill="none"/>
                <path d="M130 345 C150 352 180 352 206 344" stroke="#FF8FA8" stroke-width="7" fill="none"/>
                <!-- K I D blocks -->
                <g font-family="Georgia, serif" font-weight="700" font-size="34" fill="#fff" text-anchor="middle">
                    <rect x="350" y="252" width="56" height="56" rx="10" fill="#FF7A45" transform="rotate(-8 378 280)"/>
                    <text x="378" y="292" transform="rotate(-8 378 280)">K</text>
                    <rect x="336" y="312" width="56" height="56" rx="10" fill="#22B573" transform="rotate(10 364 340)"/>
                    <text x="364" y="352" transform="rotate(10 364 340)">I</text>
                    <rect x="398" y="322" width="56" height="56" rx="10" fill="#3D7BF5" transform="rotate(-12 426 350)"/>
                    <text x="426" y="362" transform="rotate(-12 426 350)">D</text>
                </g>
            </svg>
        </div>
    </div>
    <!-- smooth layered waves at the bottom edge -->
    <div class="hero-clouds" aria-hidden="true">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" focusable="false">
            <path class="hero-wave-back" d="M0 58 C 180 104 360 104 540 74 C 720 44 900 18 1080 38 C 1260 58 1350 88 1440 70 L1440 120 L0 120 Z"/>
            <path class="hero-wave-mid" d="M0 82 C 220 54 420 50 640 76 C 860 102 1060 106 1260 82 C 1350 72 1400 66 1440 66 L1440 120 L0 120 Z"/>
            <path class="hero-wave-front" d="M0 96 C 240 74 480 74 720 92 C 960 110 1200 110 1440 88 L1440 120 L0 120 Z"/>
        </svg>
    </div>
</section>

<!-- ============ Shop by age ============ -->
<?php if (!empty($ageGroups)): ?>
<section class="shop-section" id="shop-by-age">
    <div class="container">
        <div class="section-head is-center">
            <span class="eyebrow">Shop by age</span>
            <h2>Which stage is your little one at?</h2>
            <p>Every stage is filled with toys that are safe, fun and just challenging enough.</p>
        </div>

        <div class="age-grid">
            <?php foreach ($ageGroups as $i => $group): ?>
                <?php
                $look  = $ageLooks[$i % count($ageLooks)];
                $count = (int) $group['toy_count'];
                ?>
                <a class="age-card age-<?= $look['color'] ?>"
                   href="<?= BASE_URL ?>/products/index.php?age=<?= urlencode($group['slug']) ?>">
                    <div class="age-card-top">
                        <span class="age-card-pic">
                            <?php if (!empty($group['image'])): ?>
                                <img src="<?= htmlspecialchars(shopImageUrl($group['image'])) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span aria-hidden="true"><?= $look['emoji'] ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="age-card-range"><?= (int) $group['min_age'] ?>–<?= (int) $group['max_age'] ?></span>
                    </div>
                    <div class="age-card-body">
                        <h3><?= htmlspecialchars($group['name']) ?></h3>
                        <p><?= htmlspecialchars($group['description']) ?></p>
                        <div class="age-card-foot">
                            <span><?= $count ?> <?= $count === 1 ? 'toy' : 'toys' ?></span>
                            <span class="age-card-go" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ Promo slider ============ -->
<section class="shop-section">
    <div class="container">
        <div class="promo" data-slider>
            <!-- Names: click one to open its card. The active name follows the auto-rotation too. -->
            <div class="promo-names" role="tablist" aria-label="Offers">
                <?php foreach (['New toys', 'Discounts', 'Cash on delivery', 'Birthday surprise'] as $i => $name): ?>
                    <button type="button" role="tab" class="promo-name promo-name-<?= ['purple', 'pink', 'green', 'orange'][$i] ?> <?= $i === 0 ? 'is-on' : '' ?>"
                            data-slide-name="<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= $name ?></button>
                <?php endforeach; ?>
            </div>

            <div class="promo-slide promo-purple is-active">
                <div class="promo-text">
                    <span class="promo-tag">Fresh on the shelf</span>
                    <h2>New toys every week</h2>
                    <p>Be the first to discover the newest toys we picked for every age.</p>
                    <a class="btn promo-btn" href="#new-arrivals">See what's new</a>
                </div>
                <span class="promo-art" aria-hidden="true">🎁</span>
            </div>
            <div class="promo-slide promo-pink">
                <div class="promo-text">
                    <span class="promo-tag">Discounts</span>
                    <h2>Happy prices this week</h2>
                    <p>Favourite toys at smaller prices — while they last.</p>
                    <a class="btn promo-btn" href="#discounts">Shop the deals</a>
                </div>
                <span class="promo-art" aria-hidden="true">🏷️</span>
            </div>
            <div class="promo-slide promo-green">
                <div class="promo-text">
                    <span class="promo-tag">Cash on delivery</span>
                    <h2>Pay when it arrives</h2>
                    <p>We deliver across Jordan and you pay cash at your door.</p>
                    <a class="btn promo-btn" href="<?= BASE_URL ?>/products/index.php">Start shopping</a>
                </div>
                <span class="promo-art" aria-hidden="true">🚚</span>
            </div>
            <div class="promo-slide promo-orange">
                <div class="promo-text">
                    <span class="promo-tag">Birthday surprise</span>
                    <h2>10% off on their big day</h2>
                    <p>Add the child's date of birth at checkout — if it's today, we take 10% off.</p>
                    <a class="btn promo-btn" href="<?= BASE_URL ?>/products/index.php">Shop now</a>
                </div>
                <span class="promo-art" aria-hidden="true">🎂</span>
            </div>

            <div class="promo-controls">
                <button type="button" class="promo-arrow" data-slide-prev aria-label="Previous offer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <span class="promo-dots">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <button type="button" class="<?= $i === 0 ? 'is-on' : '' ?>" data-slide-to="<?= $i ?>"
                                aria-label="Show offer <?= $i + 1 ?>"></button>
                    <?php endfor; ?>
                </span>
                <button type="button" class="promo-arrow" data-slide-next aria-label="Next offer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ============ Discounts ============ -->
<?php if (!empty($discounted)): ?>
<section class="shop-section" id="discounts">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Discounts</span>
                <h2>Happy prices this week</h2>
            </div>
            <a class="see-all" href="<?= BASE_URL ?>/products/index.php">See all toys →</a>
        </div>

        <div class="toy-grid">
            <?php $cardMode = 'landing'; ?>
            <?php foreach ($discounted as $toy): ?>
                <?php $cardImages = null; ?>
                <?php require __DIR__ . '/includes/toy_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ New arrivals ============ -->
<section class="shop-section" id="new-arrivals">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Fresh on the shelf</span>
                <h2>New arrivals</h2>
            </div>
            <a class="see-all" href="<?= BASE_URL ?>/products/index.php">Browse everything →</a>
        </div>

        <?php if (!empty($newArrivals)): ?>
            <div class="toy-grid">
                <?php $cardMode = 'landing'; ?>
                <?php foreach ($newArrivals as $toy): ?>
                    <?php $cardImages = null; ?>
                    <?php require __DIR__ . '/includes/toy_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="landing-empty">New toys are on their way — check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
