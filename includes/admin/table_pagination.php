<div class="admin-pagination-footer">
    <p class="admin-result-count" role="status">Showing <?= $pagination['start'] ?>–<?= $pagination['end'] ?> of <?= $pagination['total'] ?> <?= str_replace('_', ' ', $pagination['type']) ?></p>
    <?php if ($pagination['total'] > $pagination['size']): ?>
        <nav aria-label="Table pagination"><ul class="admin-pagination">
            <li><?php if ($pagination['page'] > 1): ?><a aria-label="Previous page" href="<?= adminTableEscape(adminTableUrl($pagination, ['page' => $pagination['page'] - 1])) ?>">← Previous</a><?php else: ?><span aria-disabled="true">← Previous</span><?php endif; ?></li>
            <?php $lastPageShown = 0; ?>
            <?php $visiblePages = array_unique([1, max(1, $pagination['page'] - 1), $pagination['page'], min($pagination['pages'], $pagination['page'] + 1), $pagination['pages']]); sort($visiblePages); ?>
            <?php foreach ($visiblePages as $number): ?>
                <?php if ($lastPageShown && $number > $lastPageShown + 1): ?><li><span class="admin-pagination-ellipsis">…</span></li><?php endif; ?>
                <li><?php if ($number === $pagination['page']): ?><span aria-current="page"><?= $number ?></span><?php else: ?><a aria-label="Page <?= $number ?>" href="<?= adminTableEscape(adminTableUrl($pagination, ['page' => $number])) ?>"><?= $number ?></a><?php endif; ?></li>
                <?php $lastPageShown = $number; ?>
            <?php endforeach; ?>
            <li><?php if ($pagination['page'] < $pagination['pages']): ?><a aria-label="Next page" href="<?= adminTableEscape(adminTableUrl($pagination, ['page' => $pagination['page'] + 1])) ?>">Next →</a><?php else: ?><span aria-disabled="true">Next →</span><?php endif; ?></li>
        </ul></nav>
    <?php endif; ?>
</div>
