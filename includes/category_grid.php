<?php // Every card opens the real, paginated category result set. ?>
<div class="category-discovery-grid">
<?php foreach ($categories as $category): ?>
    <a class="category-discovery-card" href="<?= BASE_URL ?>/products/index.php?category=<?= urlencode($category['slug']) ?>">
        <span class="category-art"><img src="<?= htmlspecialchars(shopCategoryIllustration($category['name'])) ?>" alt="" width="160" height="120" loading="lazy"></span>
        <span class="category-card-copy"><strong><?= htmlspecialchars($category['name']) ?></strong><small><?= (int) $category['toy_count'] ?> toys to discover</small></span>
        <span class="category-card-arrow" aria-hidden="true">↗</span>
    </a>
<?php endforeach; ?>
</div>
