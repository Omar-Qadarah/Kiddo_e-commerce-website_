<?php // Every card opens the real, paginated category result set. ?>
<div class="category-discovery-grid">
<?php foreach ($categories as $category): ?>
    <a class="category-discovery-card" href="<?= BASE_URL ?>/products/index.php?category=<?= urlencode($category['slug']) ?>">
        <span class="category-art">
            <?php if (!empty($category['image'])): ?>
                <img src="<?= htmlspecialchars(shopCategoryImageUrl($category['image']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>" width="160" height="120" loading="lazy">
            <?php else: ?>
                <img src="<?= htmlspecialchars(shopCategoryIllustration($category['name']), ENT_QUOTES, 'UTF-8') ?>" alt="" width="160" height="120" loading="lazy">
            <?php endif; ?>
        </span>
        <span class="category-card-copy"><strong><?= htmlspecialchars($category['name']) ?></strong><small><?= (int) $category['toy_count'] ?> toys to discover</small></span>
        <span class="category-card-arrow" aria-hidden="true">↗</span>
    </a>
<?php endforeach; ?>
</div>
