<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Discount.php';
$discount = new Discount($pdo);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';
    if (!is_string($id) || !ctype_digit($id) || !$discount->getById($id)) {
        $errors[] = 'Discount not found.';
    } elseif (!in_array($action, ['delete', 'activate', 'deactivate'], true)) {
        $errors[] = 'Invalid action.';
    } else {
        try {
            if ($action === 'delete') {
                $discount->delete($id);
            } else {
                $discount->setActive($id, $action === 'activate' ? 1 : 0);
            }
            header('Location: discounts.php');
            exit;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $errors[] = 'Unable to change the discount.';
        }
    }
}
require_once __DIR__ . '/../classes/AdminTable.php';
require_once __DIR__ . '/../includes/admin/table_helpers.php';
$adminTable = new AdminTable($pdo);
$pagination = $adminTable->getPage('discounts', $_GET);
$discounts = $pagination['rows'];
$pageTitle = 'Discounts';
$activePage = 'discounts';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>
<div class="admin-page-header">
<div>
<h1>Discounts</h1>
<p>Manage product and category discounts and their availability.</p>
</div>
<a class="btn btn-primary d-inline-flex align-items-center gap-2" href="add_discount.php">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Discount
</a>
</div>


<?php if (!empty($errors)): ?>
<ul class="alert alert-danger ps-5" role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin/table_toolbar.php'; ?>
<div class="admin-card"><div class="table-responsive"><table class="table admin-table align-middle admin-table-consistent admin-table-discounts"><thead><tr>
<th>ID</th><th>Title</th><th>Percentage</th><th>Product</th><th>Category</th>
<th>Start</th><th>End</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($discounts as $row): ?><tr>
<td><?= htmlspecialchars((string) ($row['id'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) ($row['title'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) ($row['percentage'] ?? '-') , ENT_QUOTES, 'UTF-8') ?>%</td>
<td><?= htmlspecialchars((string) ($row['product_name'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) ($row['category_name'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) ($row['starts_at'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) ($row['ends_at'] ?? '-') , ENT_QUOTES, 'UTF-8') ?></td>
<td><span class="badge <?= $row['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $row['is_active'] ? 'Active' : 'Inactive' ?></span></td>
<td><div class="admin-actions"><a class="btn admin-action-btn admin-action-edit" href="edit_discount.php?id=<?= (int) $row['id'] ?>" title="Edit discount" aria-label="Edit discount"><i class="bi bi-pencil" aria-hidden="true"></i></a>
<form method="post" class="admin-actions">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">">
<input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
<button class="btn admin-action-btn admin-action-toggle" name="action" value="<?= $row['is_active'] ? 'deactivate' : 'activate' ?>" title="<?= $row['is_active'] ? 'Deactivate discount' : 'Activate discount' ?>" aria-label="<?= $row['is_active'] ? 'Deactivate discount' : 'Activate discount' ?>"><i class="bi bi-<?= $row['is_active'] ? 'eye-slash' : 'eye' ?>" aria-hidden="true"></i></button>
<button class="btn admin-action-btn admin-action-delete" name="action" value="delete" title="Delete discount" aria-label="Delete discount"><i class="bi bi-trash" aria-hidden="true"></i></button>
</form></div></td></tr><?php endforeach; ?>
<?php if (empty($discounts)): ?><tr><td colspan="9">No discounts found.</td></tr><?php endif; ?>
</tbody></table></div><?php require __DIR__ . '/../includes/admin/table_pagination.php'; ?></div><?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
