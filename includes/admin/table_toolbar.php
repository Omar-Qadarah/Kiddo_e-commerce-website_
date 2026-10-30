<?php
$searchLabels = ['products' => 'Search products...', 'orders' => 'Search order ID or customer...', 'users' => 'Search name, email or phone...',
    'categories' => 'Search categories...', 'age_groups' => 'Search age groups...', 'discounts' => 'Search discounts or targets...',
    'reviews' => 'Search product, customer or comment...', 'order_items' => 'Search order products...'];
?>
<form class="admin-table-controls" method="GET" action="<?= adminTableEscape(basename($_SERVER['SCRIPT_NAME'])) ?>" role="search" aria-label="Search table">
    <?php foreach ($pagination['query'] as $key => $value): ?>
        <?php if (!in_array($key, ['search', 'per_page', 'category_id', 'stock'], true) && $value !== ''): ?>
            <input type="hidden" name="<?= adminTableEscape($key) ?>" value="<?= adminTableEscape($value) ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <label class="visually-hidden" for="table-search">Search records</label>
    <input class="form-control admin-table-search" id="table-search" type="search" name="search" maxlength="120" placeholder="<?= $searchLabels[$pagination['type']] ?>" value="<?= adminTableEscape($pagination['search']) ?>">
    <?php foreach ($pagination['filters'] as $filter): ?>
        <label class="visually-hidden" for="table-<?= $filter['name'] ?>"><?= $filter['label'] ?></label>
        <select class="form-select admin-table-filter" id="table-<?= $filter['name'] ?>" name="<?= $filter['name'] ?>">
            <?php foreach ($filter['options'] as $value => $label): ?>
                <option value="<?= adminTableEscape($value) ?>" <?= (string) $value === (string) $filter['value'] ? 'selected' : '' ?>><?= adminTableEscape($label) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endforeach; ?>
    <label class="admin-page-size" for="table-size">Show
        <select class="form-select" id="table-size" name="per_page" onchange="this.form.requestSubmit()">
            <?php foreach ([10, 20, 50] as $size): ?><option value="<?= $size ?>" <?= $size === $pagination['size'] ? 'selected' : '' ?>><?= $size ?></option><?php endforeach; ?>
        </select>
    </label>
    <button class="btn btn-outline-primary" type="submit">Search</button>
</form>
