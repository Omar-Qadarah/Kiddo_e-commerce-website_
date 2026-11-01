<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/shop_functions.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Product.php';

$product = new Product($pdo);
require_once __DIR__ . '/../classes/AdminTable.php';
require_once __DIR__ . '/../includes/admin/table_helpers.php';
$adminTable = new AdminTable($pdo);
$pagination = $adminTable->getPage('products', $_GET);
$products = $pagination['rows'];

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
$pageTitle = 'Products';
$activePage = 'products';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="product-page">

    <div class="admin-page-header">
        <div>
<h1>Products</h1>
<p>Manage your toys, prices, stock and product visibility.</p>
</div>

        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="add_product.php">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            Add Toy
        </a>
    </div>

    <?php require __DIR__ . '/../includes/admin/table_toolbar.php'; ?>
<div class="admin-card">
        <div class="table-responsive"><table class="table admin-table align-middle admin-table-consistent admin-table-products">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Ages</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($products)): ?>

                <?php foreach ($products as $item): ?>
                    <tr>
                        <td>
                            <img class="rounded border object-fit-cover" width="64" height="64" src="<?= h(shopImageUrl($item['image'] ?? null)) ?>" alt="<?= h($item['name']) ?>">
                        </td>

                        <td><?= h($item['name']) ?></td>

                        <td><?= h($item['category_name'] ?? 'Uncategorized') ?></td>

                        <td>
                            <?= h($item['min_age'] ?? 0) ?>–<?= h($item['max_age'] ?? 0) ?>
                        </td>

                        <td>
                            <?= number_format((float)$item['price'], 2) ?> JOD
                        </td>

                        <td><?= h($item['stock']) ?></td>

                        <td>
                            <?php if (!empty($item['is_active'])): ?>
                                <span class="badge bg-success">Visible</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Hidden</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="admin-actions">

                                <a
                                    class="btn admin-action-btn admin-action-edit"
                                    href="edit_product.php?id=<?= (int)$item['id'] ?>"
                                    title="Edit product"
                                    aria-label="Edit product"
                                ><i class="bi bi-pencil" aria-hidden="true"></i></a>

                                <form
                                    method="post"
                                    action="delete_product.php"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn admin-action-btn admin-action-delete"
                                        title="Delete product"
                                        aria-label="Delete product"
                                    ><i class="bi bi-trash" aria-hidden="true"></i></button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-4">
                        No products found. Click "Add Toy" to add your first product.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table></div><?php require __DIR__ . '/../includes/admin/table_pagination.php'; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
