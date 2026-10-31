<?php
// Same category navigation on discovery, filtered toys and age pages.
// The calling page supplies $categoryUrl so its existing route/state is kept.
$allCount = array_sum(array_column($categories, 'toy_count'));
?>
    <aside class="toys-sidebar" id="toy-types" aria-label="Categories">
        <div class="toys-sidebar-inner">
            <h2 class="toys-sidebar-title">Categories</h2>
            <ul class="cat-list">
                <li>
                    <a class="cat-link <?= $activeCategory ? '' : 'is-active' ?>"
                       href="<?= $categoryUrl('') ?>"
                       <?= $activeCategory ? '' : 'aria-current="page"' ?>>
                        <span class="cat-icon"><span aria-hidden="true">✦</span></span>
                        <span class="cat-name">All toys</span>
                        <span class="cat-count"><?= (int) $allCount ?></span>
                    </a>
                </li>
                <?php foreach ($categories as $category): ?>
                    <?php $isActive = $activeCategory && (int) $activeCategory['id'] === (int) $category['id']; ?>
                    <li>
                        <a class="cat-link <?= $isActive ? 'is-active' : '' ?>"
                           href="<?= $categoryUrl($category['slug']) ?>"
                           <?= $isActive ? 'aria-current="page"' : '' ?>>
                            <span class="cat-icon">
                                <?php if (!empty($category['image'])): ?>
                                    <img src="<?= htmlspecialchars(shopCategoryImageUrl($category['image']), ENT_QUOTES, 'UTF-8') ?>" alt="" width="32" height="32">
                                <?php else: ?>
                                    <span aria-hidden="true">🧸</span>
                                <?php endif; ?>
                            </span>
                            <span class="cat-name"><?= htmlspecialchars($category['name']) ?></span>
                            <?php if ((int) $category['toy_count'] > 0): ?>
                                <span class="cat-count"><?= (int) $category['toy_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
