<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/account_helpers.php';
require_once __DIR__ . '/../classes/Review.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId <= 0) {
    header('Location: login.php');
    exit;
}

$reviewModel = new Review($pdo);
$errors = [];
$profileFormValues = [];
$successMsg = $_SESSION['success_message'] ?? '';
$errorMsg   = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    checkCsrf();

    if ($_POST['action'] === 'update_profile') {
        $firstName = trim(is_string($_POST['first_name'] ?? null) ? $_POST['first_name'] : '');
        $lastName  = trim(is_string($_POST['last_name'] ?? null) ? $_POST['last_name'] : '');
        $email     = strtolower(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''));
        $phone     = trim(is_string($_POST['phone'] ?? null) ? $_POST['phone'] : '');
        $birthDate = trim(is_string($_POST['birth_date'] ?? null) ? $_POST['birth_date'] : '');
        $profileFormValues = compact('firstName', 'lastName', 'email', 'phone', 'birthDate');
        $profileImageExtension = null;

        $firstNameLength = function_exists('mb_strlen') ? mb_strlen($firstName, 'UTF-8') : strlen($firstName);
        $lastNameLength = function_exists('mb_strlen') ? mb_strlen($lastName, 'UTF-8') : strlen($lastName);
        if ($firstName === '' || $firstNameLength > 50) {
            $errors[] = 'Enter a first name using 1 to 50 characters.';
        }
        if ($lastName === '' || $lastNameLength > 50) {
            $errors[] = 'Enter a last name using 1 to 50 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
            $errors[] = 'Enter a valid email address (up to 120 characters).';
        } else {
            $emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $emailCheck->execute([$email, $userId]);
            if ($emailCheck->fetchColumn()) {
                $errors[] = 'That email address is already used by another account.';
            }
        }
        if (strlen($phone) > 20) {
            $errors[] = 'Phone number must be 20 characters or fewer.';
        }
        if ($birthDate !== '') {
            $parsedBirthDate = DateTime::createFromFormat('!Y-m-d', $birthDate);
            if (!$parsedBirthDate || $parsedBirthDate->format('Y-m-d') !== $birthDate || $parsedBirthDate > new DateTime('today')) {
                $errors[] = 'Choose a valid date that is not in the future.';
            }
        }

        $upload = $_FILES['profile_image'] ?? null;
        if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $errors[] = 'The profile photo could not be uploaded. Please try another image.';
            } elseif ((int)($upload['size'] ?? 0) > 3 * 1024 * 1024) {
                $errors[] = 'Profile photos must be 3 MB or smaller.';
            } else {
                $temporaryFile = (string)($upload['tmp_name'] ?? '');
                $imageInfo = $temporaryFile !== '' ? @getimagesize($temporaryFile) : false;
                $fileInfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
                $mimeType = $fileInfo && $temporaryFile !== '' ? $fileInfo->file($temporaryFile) : (string)($imageInfo['mime'] ?? '');
                $allowedImageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!$imageInfo || !isset($allowedImageTypes[$mimeType]) || ($imageInfo['mime'] ?? '') !== $mimeType) {
                    $errors[] = 'Use a valid JPG, PNG, or WebP photo.';
                } elseif ((int)$imageInfo[0] > 4000 || (int)$imageInfo[1] > 4000) {
                    $errors[] = 'Profile photo dimensions must be 4000 × 4000 pixels or smaller.';
                } else {
                    $profileImageExtension = $allowedImageTypes[$mimeType];
                }
            }
        }

        if (empty($errors)) {
            try {
                $newProfileImage = null;
                if ($profileImageExtension !== null) {
                    $profileDirectory = __DIR__ . '/../uploads/profiles';
                    if (!is_dir($profileDirectory) && !@mkdir($profileDirectory, 0755, true) && !is_dir($profileDirectory)) {
                        throw new RuntimeException('Unable to create the profile photo folder.');
                    }
                    $newProfileImage = sprintf('user_%d_%s.%s', $userId, bin2hex(random_bytes(8)), $profileImageExtension);
                    if (!move_uploaded_file($temporaryFile, $profileDirectory . '/' . $newProfileImage)) {
                        throw new RuntimeException('Unable to save the uploaded profile photo.');
                    }
                }

                $updateSql = "UPDATE users
                              SET first_name = :first_name,
                                  last_name = :last_name,
                                  email = :email,
                                  phone = :phone,
                                  birth_date = :birth_date
                              WHERE id = :id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':first_name' => $firstName,
                    ':last_name'  => $lastName,
                    ':email'      => $email,
                    ':phone'      => !empty($phone) ? $phone : null,
                    ':birth_date' => !empty($birthDate) ? $birthDate : null,
                    ':id'         => $userId,
                ]);

                if ($newProfileImage !== null) {
                    foreach (glob(__DIR__ . '/../uploads/profiles/user_' . $userId . '_*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [] as $oldImage) {
                        if (basename($oldImage) !== $newProfileImage && is_file($oldImage)) {
                            @unlink($oldImage);
                        }
                    }
                }

                $_SESSION['user_name'] = trim($firstName . ' ' . $lastName);
                $_SESSION['success_message'] = 'Profile updated successfully.';
                header('Location: profile.php');
                exit;
            } catch (Throwable $e) {
                error_log("Profile Update Error: " . $e->getMessage());
                $errors[] = $e instanceof PDOException && $e->getCode() === '23000'
                    ? 'That email address is already used by another account.'
                    : 'An error occurred while updating your profile. Please try again.';
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
            $errors[] = 'Only verified buyers with a delivered order can review this product.';
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

$profileImageUrl = accountProfileImageUrl($userId);
$profileInitials = accountUserInitials(trim($user['first_name'] . ' ' . $user['last_name']));
$activeAccountTab = 'profile';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_review') {
    $activeAccountTab = 'orders';
} elseif (isset($_GET['tab']) && in_array($_GET['tab'], ['orders', 'reviews', 'profile'], true)) {
    $activeAccountTab = $_GET['tab'];
}
$pageTitle = 'My Profile';
$activePage = 'profile';
$pageStyles = ['toys-page.css', 'profile.css'];
require_once __DIR__ . '/../includes/shop_header.php';
?>


<div class="toys-layout kiddo-account-layout">
    <aside class="toys-sidebar" aria-label="My account sections">
        <div class="toys-sidebar-inner">
            <h2 class="toys-sidebar-title"><span aria-hidden="true">🧸</span> My account</h2>
            <ul class="cat-list account-sidebar-list">
                <li>
                    <button type="button" class="cat-link account-tab-link account-sidebar-link <?= $activeAccountTab === 'orders' ? 'is-active' : '' ?>" onclick="switchTab('orders', this)" aria-controls="tab-orders" <?= $activeAccountTab === 'orders' ? 'aria-current="page"' : '' ?>>
                        <span class="cat-icon" aria-hidden="true">🛍️</span><span class="cat-name">My Orders</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="cat-link account-tab-link account-sidebar-link <?= $activeAccountTab === 'reviews' ? 'is-active' : '' ?>" onclick="switchTab('reviews', this)" aria-controls="tab-reviews" <?= $activeAccountTab === 'reviews' ? 'aria-current="page"' : '' ?>>
                        <span class="cat-icon" aria-hidden="true">⭐</span><span class="cat-name">My Reviews</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="cat-link account-tab-link account-sidebar-link <?= $activeAccountTab === 'profile' ? 'is-active' : '' ?>" onclick="switchTab('profile', this)" aria-controls="tab-profile" <?= $activeAccountTab === 'profile' ? 'aria-current="page"' : '' ?>>
                        <span class="cat-icon" aria-hidden="true">👤</span><span class="cat-name">My Profile</span>
                    </button>
                </li>
            </ul>
        </div>
    </aside>

    <div class="toys-main kiddo-account-main">
      <div class="kiddo-account-wrapper">
       <div class="container kiddo-account-container">
        <header class="kiddo-account-hero">
            <div>
                <span class="kiddo-account-eyebrow">Your account</span>
                <h1 class="kiddo-page-title">My Account</h1>
                <p class="kiddo-subtitle">Manage your profile, orders, and reviews in one place.</p>
            </div>
            <div class="kiddo-account-identity">
                <span class="kiddo-profile-avatar">
                    <?php if ($profileImageUrl): ?>
                        <img src="<?= htmlspecialchars($profileImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    <?php else: ?>
                        <span><?= htmlspecialchars($profileInitials, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </span>
                <span class="kiddo-account-identity-copy">
                    <strong><?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name']), ENT_QUOTES, 'UTF-8') ?></strong>
                    <span><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></span>
                </span>
            </div>
        </header>

        <?php if (!empty($successMsg)): ?>
            <div class="kiddo-form-alert is-success" role="status"><?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="kiddo-form-alert is-error" role="alert">
                <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="kiddo-form-alert is-error" role="alert">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div id="tab-orders" style="<?= $activeAccountTab === 'orders' ? '' : 'display:none;' ?>">
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
                                <span class="kiddo-badge <?= $ord['status'] === 'delivered' ? 'badge-status-completed' : 'badge-status-placed' ?>">
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
                                        <?php elseif ($ord['status'] !== 'delivered'): ?>
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

        <div id="tab-reviews" style="<?= $activeAccountTab === 'reviews' ? '' : 'display:none;' ?>">
            <div class="kiddo-card" style="gap: 16px;">
                <div class="reviews-panel-head">
                    <div>
                        <h5 class="fw-bold mb-2 text-dark">Submitted Reviews</h5>
                        <p class="review-panel-hint">Share feedback on toys from your delivered orders.</p>
                    </div>
                    <button type="button" class="kiddo-btn-review" onclick="switchTab('orders', null)">Review a purchase</button>
                </div>

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

        <div id="tab-profile" style="<?= $activeAccountTab === 'profile' ? '' : 'display:none;' ?>">
            <div class="profile-form-wrapper">
                <h4 class="profile-section-title">Personal Information</h4>
                <p class="profile-section-desc">Manage your account details and contact information.</p>

                <form method="POST" enctype="multipart/form-data" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="profile-photo-field">
                        <span class="kiddo-profile-avatar profile-photo-preview" id="profilePhotoPreview">
                            <?php if ($profileImageUrl): ?>
                                <img src="<?= htmlspecialchars($profileImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile photo preview">
                            <?php else: ?>
                                <span><?= htmlspecialchars($profileInitials, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </span>
                        <div class="profile-photo-control">
                            <label class="kiddo-label" for="profile-image">Profile photo</label>
                            <input id="profile-image" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp">
                            <small>JPG, PNG, or WebP · up to 3 MB</small>
                        </div>
                    </div>

                    <div class="profile-fields-grid">
                        <div class="col-md-6">
                            <div class="kiddo-form-group">
                                <label class="kiddo-label" for="first-name">First name</label>
                                <input id="first-name" type="text" name="first_name" maxlength="50" class="kiddo-control" value="<?= htmlspecialchars($profileFormValues['firstName'] ?? $user['first_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kiddo-form-group">
                                <label class="kiddo-label" for="last-name">Last name</label>
                                <input id="last-name" type="text" name="last_name" maxlength="50" class="kiddo-control" value="<?= htmlspecialchars($profileFormValues['lastName'] ?? $user['last_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="kiddo-form-group">
                        <label class="kiddo-label" for="profile-email">Email address</label>
                        <input id="profile-email" type="email" name="email" maxlength="120" class="kiddo-control" value="<?= htmlspecialchars($profileFormValues['email'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="kiddo-form-group">
                        <label class="kiddo-label" for="profile-phone">Phone number</label>
                        <input id="profile-phone" type="tel" name="phone" maxlength="20" class="kiddo-control" placeholder="+962 7X XXX XXXX" value="<?= htmlspecialchars($profileFormValues['phone'] ?? $user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="kiddo-form-group">
                        <label class="kiddo-label" for="birth-date">Child's birth date</label>
                        <input id="birth-date" type="date" name="birth_date" class="kiddo-control" value="<?= htmlspecialchars($profileFormValues['birthDate'] ?? $user['birth_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <small class="profile-field-hint">Used for birthday discounts.</small>
                    </div>

                    <div class="profile-form-actions">
                        <button type="submit" class="kiddo-btn-save">Save changes</button>
                    </div>
                </form>
            </div>
        </div>

       </div>
      </div>
    </div>
</div>

<div class="kiddo-modal-bg" id="reviewModal">
    <div class="kiddo-modal-window">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">Rate Product</h5>
            <button type="button" class="btn-close" onclick="closeReviewModal()" aria-label="Close review form">&times;</button>
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
    
    document.querySelectorAll('.account-tab-link').forEach(function(b) {
        b.classList.toggle('is-active', b === btn);
        if (b === btn) b.setAttribute('aria-current', 'page');
        else b.removeAttribute('aria-current');
    });

    document.getElementById('tab-' + tabName).style.display = 'block';
}

function openReviewModal(productId, productName) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

const profileImageInput = document.getElementById('profile-image');
const profileImagePreview = document.getElementById('profilePhotoPreview');
if (profileImageInput && profileImagePreview) {
    profileImageInput.addEventListener('change', () => {
        const file = profileImageInput.files && profileImageInput.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        const imageUrl = URL.createObjectURL(file);
        const previewImage = document.createElement('img');
        previewImage.alt = 'Selected profile photo preview';
        previewImage.onload = () => URL.revokeObjectURL(imageUrl);
        previewImage.src = imageUrl;
        profileImagePreview.replaceChildren(previewImage);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
