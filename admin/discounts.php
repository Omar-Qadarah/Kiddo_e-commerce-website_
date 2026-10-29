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
$discounts = $pdo->query('SELECT discounts.*, products.name AS product_name,
    categories.name AS category_name FROM discounts
    LEFT JOIN products ON products.id = discounts.product_id
    LEFT JOIN categories ON categories.id = discounts.category_id
    ORDER BY discounts.id DESC')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Discounts';
$activePage = 'discounts';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<?php if (!empty($errors)): ?>
<ul class="alert alert-danger ps-5" role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<p><a class="btn btn-primary" href="add_discount.php">Add Discount</a></p>
<div class="admin-card"><div class="table-responsive"><table class="table admin-table align-middle"><thead><tr>
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
<td><a class="btn btn-sm btn-warning mb-2" href="edit_discount.php?id=<?= (int) $row['id'] ?>">Edit</a>
<form method="post" class="d-flex flex-wrap gap-2">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
<button class="btn btn-sm btn-outline-primary" name="action" value="<?= $row['is_active'] ? 'deactivate' : 'activate' ?>">
<?= $row['is_active'] ? 'Deactivate' : 'Activate' ?></button>
<button class="btn btn-sm btn-danger" name="action" value="delete" onclick="return confirm('Delete this discount permanently?')">Delete</button>
</form></td></tr><?php endforeach; ?>
<?php if (empty($discounts)): ?><tr><td colspan="9">No discounts found.</td></tr><?php endif; ?>
</tbody></table></div></div><?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
