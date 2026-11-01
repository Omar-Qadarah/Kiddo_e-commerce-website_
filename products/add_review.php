<?php
 
 
 
 
 
 
 
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
require_once __DIR__ . '/../classes/Review.php';

$isPost    = $_SERVER['REQUEST_METHOD'] === 'POST';
$productId = $isPost
    ? filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT)
    : filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1) {
    header('Location: ' . BASE_URL . '/products/index.php');
    exit;
}

$toyPage = BASE_URL . '/products/details.php?id=' . $productId . '#reviews';

 
$userId = $_SESSION['user_id'] ?? null;
if (!is_scalar($userId) || !ctype_digit((string) $userId)) {
     
    $_SESSION['after_login'] = $toyPage;
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
$userId = (int) $userId;

 
if (!$isPost) {
    header('Location: ' . $toyPage);
    exit;
}

checkCsrf();

 
function reviewFlash(string $type, string $message, array $old = []): void
{
    $_SESSION['review_flash'] = ['type' => $type, 'message' => $message, 'old' => $old];
}

$catalog     = new ToyCatalog($pdo);
$reviewModel = new Review($pdo);

$rating  = trim((string) ($_POST['rating'] ?? ''));
$comment = trim((string) ($_POST['comment'] ?? ''));
$old     = ['rating' => $rating, 'comment' => $comment];

 
$userCheck = $pdo->prepare('SELECT id FROM users WHERE id = ? AND is_active = 1');
$userCheck->execute([$userId]);

if (!$userCheck->fetch()) {
    reviewFlash('error', 'Please log in again to write a review.');
} elseif (!$catalog->getToy($productId)) {
    reviewFlash('error', 'This toy is not available.');
} elseif ($reviewModel->hasReviewed($userId, $productId)) {
    reviewFlash('error', 'You have already reviewed this toy. Thank you!');
} elseif (!$reviewModel->isValidRating($rating)) {
    reviewFlash('error', 'Please choose a rating from 1 to 5 stars.', $old);
} elseif (mb_strlen($comment) < 3) {
    reviewFlash('error', 'Please write a comment (at least 3 characters).', $old);
} elseif (mb_strlen($comment) > 500) {
    reviewFlash('error', 'Your comment is too long (500 characters maximum).', $old);
} else {
     
    try {
        $insert = $pdo->prepare(
            'INSERT INTO reviews (product_id, user_id, rating, comment, is_visible, created_at)
             VALUES (?, ?, ?, ?, 1, NOW())'
        );
        $insert->execute([$productId, $userId, (int) $rating, $comment]);
        reviewFlash('success', 'Thank you! Your review was added.');
    } catch (PDOException $e) {
        error_log('Add review failed: ' . $e->getMessage());
        reviewFlash('error', 'Sorry, your review could not be saved. Please try again.', $old);
    }
}

header('Location: ' . $toyPage);
exit;
