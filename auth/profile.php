<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Review.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId <= 0) {
    header('Location: login.php');
    exit;
}

$reviewModel = new Review($pdo);
$errors = [];
$successMsg = $_SESSION['success_message'] ?? '';
$errorMsg   = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    checkCsrf();

    if ($_POST['action'] === 'update_profile') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $birthDate = trim($_POST['birth_date'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            $errors[] = 'First name and last name are required.';
        }

        if (empty($errors)) {
            try {
                $updateSql = "UPDATE users 
                              SET first_name = :first_name, 
                                  last_name = :last_name, 
                                  phone = :phone, 
                                  birth_date = :birth_date 
                              WHERE id = :id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':first_name' => $firstName,
                    ':last_name'  => $lastName,
                    ':phone'      => !empty($phone) ? $phone : null,
                    ':birth_date' => !empty($birthDate) ? $birthDate : null,
                    ':id'         => $userId
                ]);

                $_SESSION['user_name'] = $firstName;
                $_SESSION['success_message'] = 'Profile updated successfully.';
                header('Location: profile.php');
                exit;
            } catch (PDOException $e) {
                error_log("Profile Update Error: " . $e->getMessage());
                $errors[] = 'An error occurred while updating your profile.';
            }
        }
    } elseif ($_POST['action'] === 'submit_review') {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $rating    = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        $comment   = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if (!$productId || $productId <= 0) {
            $errors[] = 'Invalid product.';
        } elseif (!$rating || !$reviewModel->isValidRating($rating)) {
            $errors[] = 'Please select a valid rating between 1 and 5 stars.';
        } elseif (empty($comment)) {
            $errors[] = 'Please write a brief comment sharing your experience.';
        } elseif ($reviewModel->hasReviewed($userId, $productId)) {
            $errors[] = 'You have already submitted a review for this product.';
        } elseif (!$reviewModel->isEligibleToReview($userId, $productId)) {
            $errors[] = 'Only verified buyers with a completed order can review this product.';
        } else {
            if ($reviewModel->createReview($productId, $userId, $rating, $comment)) {
                $_SESSION['success_message'] = 'Thank you! Your review has been submitted successfully.';
                header('Location: profile.php');
                exit;
            } else {
                $errors[] = 'An error occurred while saving your review.';
            }
        }
    }
}

$userStmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, birth_date, created_at FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

$orderItemsByOrder = [];
if (!empty($orders)) {
    $orderIds = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

    $itemsSql = "SELECT oi.*, p.image 
                 FROM order_items oi
                 LEFT JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id IN ($placeholders)";
    $itemsStmt = $pdo->prepare($itemsSql);
    $itemsStmt->execute($orderIds);
    $allItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allItems as $item) {
        $orderItemsByOrder[$item['order_id']][] = $item;
    }
}

$myReviewsStmt = $pdo->prepare("SELECT r.*, p.name AS product_name, p.image AS product_image 
                                FROM reviews r 
                                JOIN products p ON r.product_id = p.id 
                                WHERE r.user_id = ? 
                                ORDER BY r.created_at DESC");
$myReviewsStmt->execute([$userId]);
$myReviews = $myReviewsStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'My Profile';
$activePage = 'profile';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.kiddo-account-wrapper {
    background-color: #fcfbfe;
    padding: 30px 0 60px;
    min-height: 80vh;
    
}
.kiddo-page-title {
    color: #2d3436;
    font-weight: 800;
    font-size: 2rem;
    margin-bottom: 4px;
}
.kiddo-subtitle {
    color: #636e72;
    margin-bottom: 24px;
}
.kiddo-tabs-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.kiddo-tab-btn {
    border: none;
    background: #f1f2f6;
    color: #636e72;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.kiddo-tab-btn:hover {
    background: #e4e6ed;
    color: #2d3436;
}
.kiddo-tab-btn.active {
    background: #6c5ce7;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25);
}
.kiddo-card {
    display: flex;
    flex-direction: column;
    padding: 24px;
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #edf0f5;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    margin-bottom: 20px;
}
.kiddo-order-head {
    background: #fafafa;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 12px;
    margin-bottom: 16px;
    border: 1px solid #edf0f5;
}
.kiddo-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: capitalize;
}
.badge-status-completed {
    background: #eafaf1;
    color: #2ed573;
}
.badge-status-placed {
    background: #eef2f7;
    color: #747d8c;
}
.kiddo-product-row {
    padding: 14px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f8f9fa;
}
.kiddo-product-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.kiddo-thumb {
    width: 58px;
    height: 58px;
    border-radius: 12px;
    object-fit: cover;
    background: #f8f9fa;
    border: 1px solid #edf0f5;
}
.kiddo-btn-review {
    background: #6c5ce7;
    color: #fff;
    border: none;
    padding: 7px 18px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.kiddo-btn-review:hover {
    background: #5b4cdb;
    color: #fff;
}
.kiddo-badge-subtle {
    background: #f1f2f6;
    color: #747d8c;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}
.reviews-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.review-entry-box {
    background: #ffffff;
    border: 1px solid #edf0f5;
    border-radius: 14px;
    padding: 18px 20px;
    transition: box-shadow 0.2s ease;
}
.review-entry-box:hover {
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.05);
}
.review-head-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.review-product-info {
    display: flex;
    align-items: center;
    gap: 14px;
}
.review-thumb {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    object-fit: cover;
    background: #f8f9fa;
    border: 1px solid #edf0f5;
    flex-shrink: 0;
}
.review-product-title {
    font-size: 1rem;
    font-weight: 700;
    color: #2d3436;
    margin: 0 0 4px 0;
    text-decoration: none;
    display: block;
}
.review-product-title:hover {
    color: #6c5ce7;
}
.review-date {
    font-size: 0.8rem;
    color: #8395a7;
    margin: 0;
}
.review-stars {
    color: #f1c40f !important;
    font-size: 1.1rem;
    letter-spacing: 2px;
    margin-bottom: 8px;
    line-height: 1;
}
.review-stars .star-empty {
    color: #dfe6e9 !important;
}
.review-comment {
    color: #4b5563;
    font-size: 0.95rem;
    line-height: 1.55;
    margin: 0;
}
.profile-form-wrapper {
    background: #ffffff;
    border: 1px solid #edf0f5;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(108, 92, 231, 0.04);
}
.profile-section-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #2d3436;
    margin-bottom: 6px;
}
.profile-section-desc {
    font-size: 0.9rem;
    color: #8395a7;
    margin-bottom: 24px;
}
.kiddo-form-group {
    margin-bottom: 20px;
}
.kiddo-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #576574;
    margin-bottom: 8px;
}
.kiddo-control {
    width: 100%;
    height: 48px;
    background-color: #fafbfc;
    border: 1.5px solid #e4e7eb;
    border-radius: 12px;
    padding: 0 16px;
    font-size: 0.95rem;
    color: #2d3436;
    transition: all 0.2s ease-in-out;
}
.kiddo-control:focus {
    background-color: #ffffff;
    border-color: #6c5ce7;
    outline: none;
    box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.12);
}
.kiddo-control:disabled,
.kiddo-control[readonly] {
    background-color: #f1f2f6;
    border-color: #e4e7eb;
    color: #8395a7;
    cursor: not-allowed;
}
.kiddo-btn-save {
    background-color: #6c5ce7;
    color: #ffffff;
    border: none;
    height: 48px;
    padding: 0 32px;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25);
    transition: all 0.2s ease;
}
.kiddo-btn-save:hover {
    background-color: #5b4cdb;
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.35);
    transform: translateY(-1px);
}
.kiddo-modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(45, 52, 54, 0.45);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.kiddo-modal-window {
    background: #ffffff;
    border-radius: 20px;
    max-width: 480px;
    width: 90%;
    padding: 26px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}
</style>

<div class="kiddo-account-wrapper">
    <div class="container" style="max-width: 960px;">
        <h1 class="kiddo-page-title">My Account</h1>
        <p class="kiddo-subtitle">Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></p>

        <?php if (!empty($successMsg)): ?>
    <div style="display: flex; justify-content: start; width: 100%; margin-bottom: 24px;">
        <div id="successAlertBox" style="display: inline-flex; align-items: center; justify-content: space-between; gap: 14px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 12px 18px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08); font-size: 0.95rem; font-weight: 600; color: #065f46; max-width: 90%; min-width: 320px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <button type="button" onclick="document.getElementById('successAlertBox').remove();" aria-label="Close" style="background: transparent; border: none; font-size: 1.25rem; line-height: 1; color: #047857; cursor: pointer; padding: 0 4px; display: flex; align-items: center; justify-content: center; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                &times;
            </button>
        </div>
    </div>
<?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="kiddo-tabs-bar">
            <button type="button" class="kiddo-tab-btn active" onclick="switchTab('orders', this)">My Orders</button>
            <button type="button" class="kiddo-tab-btn" onclick="switchTab('reviews', this)">My Reviews</button>
            <button type="button" class="kiddo-tab-btn" onclick="switchTab('profile', this)">Profile Settings</button>
        </div>

        <div id="tab-orders">
            <?php if (empty($orders)): ?>
                <div class="kiddo-card p-5 text-center">
                    <h5 class="fw-bold text-dark">No orders found</h5>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="../products/index.php" class="kiddo-btn-save text-decoration-none d-inline-flex align-items-center justify-content-center" style="width: auto; min-width: 150px; margin: 0 auto; padding: 12px 28px;">Browse Toys</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $ord): ?>
                    <div class="kiddo-card">
                        <div class="kiddo-order-head">
                            <div>
                                <span class="fw-bold text-dark">Order #<?= (int)$ord['id'] ?></span>
                                <span class="text-muted small ms-2"><?= date('M j, Y', strtotime($ord['created_at'])) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="kiddo-badge <?= $ord['status'] === 'completed' ? 'badge-status-completed' : 'badge-status-placed' ?>">
                                    <?= ucfirst(htmlspecialchars($ord['status'], ENT_QUOTES, 'UTF-8')) ?>
                                </span>
                                <span class="fw-bold text-dark"><?= number_format((float)$ord['total'], 2) ?> JOD</span>
                            </div>
                        </div>

                        <div>
                            <?php 
                            $items = $orderItemsByOrder[$ord['id']] ?? [];
                            foreach ($items as $it): 
                                $canReview = $reviewModel->canUserReview($userId, $it['product_id']);
                                $alreadyReviewed = $reviewModel->hasReviewed($userId, $it['product_id']);
                                
                                $imgName = !empty($it['image']) ? basename($it['image']) : '';
                                $productImg = !empty($imgName) && file_exists(__DIR__ . '/../uploads/products/' . $imgName)
                                    ? '../uploads/products/' . $imgName 
                                    : '../assets/images/toy-placeholder.svg';
                            ?>
                                <div class="kiddo-product-row">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($productImg, ENT_QUOTES, 'UTF-8') ?>" 
                                             alt="" class="kiddo-thumb" 
                                             onerror="this.src='../assets/images/toy-placeholder.svg';">
                                        <div>
                                            <a href="../products/details.php?id=<?= (int)$it['product_id'] ?>" class="text-decoration-none text-dark">
                                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($it['product_name'], ENT_QUOTES, 'UTF-8') ?></h6>
                                            </a>
                                            <div class="text-muted small">Qty: <?= (int)$it['quantity'] ?> × <?= number_format((float)$it['unit_price'], 2) ?> JOD</div>
                                        </div>
                                    </div>

                                    <div>
                                        <?php if ($canReview): ?>
                                            <button type="button" class="kiddo-btn-review" 
                                                    onclick="openReviewModal(<?= (int)$it['product_id'] ?>, '<?= htmlspecialchars(addslashes($it['product_name']), ENT_QUOTES, 'UTF-8') ?>')">
                                                Rate & Review
                                            </button>
                                        <?php elseif ($alreadyReviewed): ?>
                                            <span class="kiddo-badge-subtle">Reviewed</span>
                                        <?php elseif ($ord['status'] !== 'completed'): ?>
                                            <span class="kiddo-badge-subtle">Awaiting Delivery</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="tab-reviews" style="display: none;">
            <div class="kiddo-card" style="gap: 16px;">
                <h5 class="fw-bold mb-2 text-dark">Submitted Reviews</h5>

                <?php if (empty($myReviews)): ?>
                    <p class="text-muted mb-0">You have not written any reviews yet.</p>
                <?php else: ?>
                    <div class="reviews-container">
                        <?php foreach ($myReviews as $rev): 
                            $rImgName = !empty($rev['product_image']) ? basename($rev['product_image']) : '';
                            $revImg = !empty($rImgName) && file_exists(__DIR__ . '/../uploads/products/' . $rImgName)
                                ? '../uploads/products/' . $rImgName 
                                : '../assets/images/toy-placeholder.svg';
                        ?>
                            <div class="review-entry-box">
                                <div class="review-head-row">
                                    <div class="review-product-info">
                                        <img src="<?= htmlspecialchars($revImg, ENT_QUOTES, 'UTF-8') ?>" 
                                             alt="" class="review-thumb"
                                             onerror="this.src='../assets/images/toy-placeholder.svg';">
                                        <div>
                                            <a href="../products/details.php?id=<?= (int)$rev['product_id'] ?>" class="review-product-title">
                                                <?= htmlspecialchars($rev['product_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <p class="review-date"><?= date('M j, Y', strtotime($rev['created_at'])) ?></p>
                                        </div>
                                    </div>

                                    <div>
                                        <?= $rev['is_visible'] 
                                            ? '<span class="kiddo-badge badge-status-completed">Visible</span>' 
                                            : '<span class="kiddo-badge badge-status-placed">Hidden by Admin</span>' ?>
                                    </div>
                                </div>

                                <div class="review-stars">
                                    <?= str_repeat('★', (int)$rev['rating']) ?><span class="star-empty"><?= str_repeat('☆', 5 - (int)$rev['rating']) ?></span>
                                </div>

                                <p class="review-comment"><?= htmlspecialchars($rev['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-profile" style="display: none;">
            <div class="profile-form-wrapper" style="max-width: 640px;">
                <h4 class="profile-section-title">Personal Information</h4>
                <p class="profile-section-desc">Manage your account details and contact information.</p>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <div class="kiddo-form-group">
                                <label class="kiddo-label">First Name</label>
                                <input type="text" name="first_name" class="kiddo-control" value="<?= htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kiddo-form-group">
                                <label class="kiddo-label">Last Name</label>
                                <input type="text" name="last_name" class="kiddo-control" value="<?= htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="kiddo-form-group">
                        <label class="kiddo-label">Email Address</label>
                        <input type="email" class="kiddo-control" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="kiddo-form-group">
                        <label class="kiddo-label">Phone Number</label>
                        <input type="tel" name="phone" class="kiddo-control" placeholder="+962 7X XXX XXXX" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="kiddo-form-group mb-4">
                        <label class="kiddo-label">Child's Birth Date</label>
                        <input type="date" name="birth_date" class="kiddo-control" value="<?= htmlspecialchars($user['birth_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <small class="text-muted" style="font-size: 0.75rem;">Used to receive exclusive birthday discounts.</small>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="kiddo-btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<div class="kiddo-modal-bg" id="reviewModal">
    <div class="kiddo-modal-window">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">Rate Product</h5>
            <button type="button" class="btn-close" onclick="closeReviewModal()"></button>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="submit_review">
            <input type="hidden" name="product_id" id="modalProductId" value="">

            <p class="text-muted small mb-3">Product: <strong id="modalProductName" class="text-dark"></strong></p>

            <div class="mb-3">
                <label class="kiddo-label">Rating</label>
                <select name="rating" class="kiddo-control" required>
                    <option value="5" selected>5 Stars - Excellent</option>
                    <option value="4">4 Stars - Good</option>
                    <option value="3">3 Stars - Average</option>
                    <option value="2">2 Stars - Poor</option>
                    <option value="1">1 Star - Terrible</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="kiddo-label">Review Comment</label>
                <textarea name="comment" class="kiddo-control" style="height: 100px; padding: 12px 16px;" placeholder="Write your feedback..." required></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" onclick="closeReviewModal()">Cancel</button>
                <button type="submit" class="kiddo-btn-save py-2 px-4" style="height: auto;">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabName, btn) {
    document.getElementById('tab-orders').style.display = 'none';
    document.getElementById('tab-reviews').style.display = 'none';
    document.getElementById('tab-profile').style.display = 'none';
    
    document.querySelectorAll('.kiddo-tab-btn').forEach(function(b) {
        b.classList.remove('active');
    });

    document.getElementById('tab-' + tabName).style.display = 'block';
    btn.classList.add('active');
}

function openReviewModal(productId, productName) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>