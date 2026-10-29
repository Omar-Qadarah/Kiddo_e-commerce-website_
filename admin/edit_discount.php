<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Discount.php';
$discount = new Discount($pdo);
$errors = [];
$id = $_GET['id'] ?? '';
if (!is_string($id) || !ctype_digit($id) || !$discountData = $discount->getById($id)) {
    http_response_code(404);
    exit('Discount not found.');
}
$title = (string) ($discountData['title'] ?? '');
$percentage = (string) ($discountData['percentage'] ?? '');
$product_id = (string) ($discountData['product_id'] ?? '');
$category_id = (string) ($discountData['category_id'] ?? '');
$starts_at = (string) ($discountData['starts_at'] ?? '');
$ends_at = (string) ($discountData['ends_at'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $title = $_POST['title'] ?? '';
    if (!is_string($title)) {
        $errors[] = 'Invalid title value.';
        $title = '';
    }
    $title = trim($title);
    $percentage = $_POST['percentage'] ?? '';
    if (!is_string($percentage)) {
        $errors[] = 'Invalid percentage value.';
        $percentage = '';
    }
    $percentage = trim($percentage);
    $product_id = $_POST['product_id'] ?? '';
    if (!is_string($product_id)) {
        $errors[] = 'Invalid product_id value.';
        $product_id = '';
    }
    $product_id = trim($product_id);
    $category_id = $_POST['category_id'] ?? '';
    if (!is_string($category_id)) {
        $errors[] = 'Invalid category_id value.';
        $category_id = '';
    }
    $category_id = trim($category_id);
    $starts_at = $_POST['starts_at'] ?? '';
    if (!is_string($starts_at)) {
        $errors[] = 'Invalid starts_at value.';
        $starts_at = '';
    }
    $starts_at = trim($starts_at);
    $ends_at = $_POST['ends_at'] ?? '';
    if (!is_string($ends_at)) {
        $errors[] = 'Invalid ends_at value.';
        $ends_at = '';
    }
    $ends_at = trim($ends_at);
    $errors = array_merge($errors, $discount->validate(
        $title, $percentage, $product_id, $category_id, $starts_at, $ends_at
    ));
    if (empty($errors)) {
        try {
            $discount->update($id, $title, $percentage,
                $product_id === '' ? null : $product_id,
                $category_id === '' ? null : $category_id,
                $starts_at === '' ? null : $starts_at,
                $ends_at === '' ? null : $ends_at);
            header('Location: discounts.php');
            exit;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $errors[] = 'Unable to save the discount. Please try again.';
        }
    }
}
$products = $pdo->query('SELECT id, name FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Edit Discount';
$activePage = 'discounts';
require_once __DIR__ . '/../includes/admin/layout_start.php';
?>
<div class="admin-page-header">
<div>
<h1>Edit Discount</h1>
<p>Update the discount details, target and validity dates.</p>
</div>
</div>


<?php if (!empty($errors)): ?>
<ul class="alert alert-danger ps-5" role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<form method="post" class="admin-card p-4">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<p class="mb-3"><label class="form-label" for="title">Title</label> <input class="form-control" id="title" name="title" maxlength="100" required value="<?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?>"></p>
<p class="mb-3"><label class="form-label" for="percentage">Percentage</label> <input class="form-control" type="number" id="percentage" name="percentage" min="0.01" max="100" step="0.01" required value="<?= htmlspecialchars((string) $percentage, ENT_QUOTES, 'UTF-8') ?>"></p>
<p>Choose one product OR one category.</p>
<p class="mb-3"><label class="form-label" for="product_id">Product</label> <select class="form-select" id="product_id" name="product_id">
<option value="">No product</option>
<?php foreach ($products as $item): ?>
<option value="<?= (int) $item['id'] ?>" <?= $product_id === (string) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select></p>
<p class="mb-3"><label class="form-label" for="category_id">Category</label> <select class="form-select" id="category_id" name="category_id">
<option value="">No category</option>
<?php foreach ($categories as $item): ?>
<option value="<?= (int) $item['id'] ?>" <?= $category_id === (string) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select></p>
<p class="mb-3"><label class="form-label" for="starts_at">Start date (optional)</label> <input class="form-control" type="date" id="starts_at" name="starts_at" value="<?= htmlspecialchars((string) $starts_at, ENT_QUOTES, 'UTF-8') ?>"></p>
<p class="mb-3"><label class="form-label" for="ends_at">End date (optional)</label> <input class="form-control" type="date" id="ends_at" name="ends_at" value="<?= htmlspecialchars((string) $ends_at, ENT_QUOTES, 'UTF-8') ?>"></p>
<button class="btn btn-primary" type="submit">Save Discount</button>
</form>
<p><a class="btn btn-light mt-3" href="discounts.php">Back to discounts</a></p>
<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
