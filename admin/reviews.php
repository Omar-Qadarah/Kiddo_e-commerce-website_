<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Review.php';
$review = new Review($pdo);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';
    $row = false;
    if (is_string($id) && ctype_digit($id)) {
        $row = $review->getById($id);
    }
    if (!$row) {
        $errors[] = 'Review not found.';
    } elseif (!in_array($action, ['hide', 'restore', 'delete'], true)) {
        $errors[] = 'Invalid action.';
    } elseif ($action === 'restore' && !$review->isValidRating($row['rating'])) {
        $errors[] = 'Cannot restore a review with an invalid rating. Ratings must be 1 to 5.';
    } else {
        try {
            if ($action === 'hide') {
                $review->hide($id);
            } elseif ($action === 'restore') {
                if (!$review->restore($id)) {
                    $errors[] = 'Review was already visible or could not be restored.';
                }
            } else {
                $review->delete($id);
            }
            if (empty($errors)) {
                header('Location: reviews.php');
                exit;
            }
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $errors[] = 'Unable to change the review.';
        }
    }
}
$reviews = $review->getAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reviews - Kiddo</title></head><body>
<nav><a href="discounts.php">Discounts</a> | <a href="reviews.php">Reviews</a></nav>
<h1>Reviews</h1>
<?php if (!empty($errors)): ?>
<ul role="alert">
<?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<p>Ratings must be whole numbers from 1 to 5. Invalid ratings are flagged and cannot be restored.</p>
<table border="1" cellpadding="8"><thead><tr>
<th>ID</th><th>Product</th><th>Customer</th><th>Rating</th><th>Comment</th>
<th>Created</th><th>Visibility</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($reviews as $row): ?><tr>
<td><?= (int) $row['id'] ?></td>
<td><?= htmlspecialchars((string) $row['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) $row['first_name'] . ' ' . $row['last_name'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?php if ($review->isValidRating($row['rating'])): ?>
<?= htmlspecialchars((string) $row['rating'], ENT_QUOTES, 'UTF-8') ?> / 5
<?php else: ?>Invalid rating (<?= htmlspecialchars((string) $row['rating'], ENT_QUOTES, 'UTF-8') ?>)<?php endif; ?></td>
<td><?= htmlspecialchars((string) $row['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars((string) $row['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?= $row['is_visible'] ? 'Visible' : 'Hidden' ?></td>
<td><form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
<?php if ($row['is_visible']): ?>
<button name="action" value="hide">Hide</button>
<?php elseif ($review->isValidRating($row['rating'])): ?>
<button name="action" value="restore">Restore</button>
<?php endif; ?>
<button name="action" value="delete" onclick="return confirm('Delete this review permanently?')">Delete</button>
</form></td></tr><?php endforeach; ?>
<?php if (empty($reviews)): ?><tr><td colspan="8">No reviews found.</td></tr><?php endif; ?>
</tbody></table></body></html>
