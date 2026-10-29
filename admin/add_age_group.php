
<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/AgeGroup.php';

$ageGroupModel = new AgeGroup($pdo);

$errors = [];

$name = '';
$slug = '';
$description = '';
$minAge = '';
$maxAge = '';
$sortOrder = '0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(trim($_POST['slug'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $minAge = trim($_POST['min_age'] ?? '');
    $maxAge = trim($_POST['max_age'] ?? '');
    $sortOrder = trim($_POST['sort_order'] ?? '0');

    // Validate name
    if ($name === '') {
        $errors[] = 'Age group name is required.';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Age group name must not exceed 100 characters.';
    }

    // Validate slug
    if ($slug === '') {
        $errors[] = 'Slug is required.';
    } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        $errors[] = 'Slug must contain lowercase letters, numbers, and single hyphens only.';
    } elseif ($ageGroupModel->slugExists($slug)) {
        $errors[] = 'This slug already exists.';
    }

    // Validate description
    if ($description === '') {
        $errors[] = 'Description is required.';
    } elseif (mb_strlen($description) > 255) {
        $errors[] = 'Description must not exceed 255 characters.';
    }

    // Validate minimum age
    $minAgeValue = filter_var($minAge, FILTER_VALIDATE_INT);

    if ($minAge === '' || $minAgeValue === false || $minAgeValue < 0) {
        $errors[] = 'Minimum age must be a non-negative whole number.';
    }

    // Validate maximum age
    $maxAgeValue = filter_var($maxAge, FILTER_VALIDATE_INT);

    if ($maxAge === '' || $maxAgeValue === false || $maxAgeValue < 0) {
        $errors[] = 'Maximum age must be a non-negative whole number.';
    }

    // Validate age range
    if (
        $minAgeValue !== false &&
        $maxAgeValue !== false &&
        $minAgeValue >= 0 &&
        $maxAgeValue >= 0 &&
        $minAgeValue > $maxAgeValue
    ) {
        $errors[] = 'Minimum age cannot be greater than maximum age.';
    }

    // Validate sort order
    $sortOrderValue = filter_var($sortOrder, FILTER_VALIDATE_INT);

    if ($sortOrderValue === false || $sortOrderValue < 0) {
        $errors[] = 'Sort order must be a non-negative whole number.';
    }

    // Validate optional image upload
    $imagePath = null;

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image size must not exceed 2 MB.';
        } else {
            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);

            if (!isset($allowedTypes[$mimeType])) {
                $errors[] = 'Only JPG, PNG, and WEBP images are allowed.';
            } else {
                $imageExtension = $allowedTypes[$mimeType];
            }
        }
    }

    // Save uploaded image and create the age group
    if (empty($errors)) {

        $uploadDirectory = __DIR__ . '/../uploads/age-groups/';

        if (!is_dir($uploadDirectory)) {
            if (!mkdir($uploadDirectory, 0755, true)) {
                $errors[] = 'Unable to create the image upload directory.';
            }
        }

        if (empty($errors) && isset($imageExtension)) {

            $imageName = bin2hex(random_bytes(16)) . '.' . $imageExtension;
            $destination = $uploadDirectory . $imageName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = 'Unable to save the uploaded image.';
            } else {
                $imagePath = 'uploads/age-groups/' . $imageName;
            }
        }

        if (empty($errors)) {
            try {
                $ageGroupModel->create([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'min_age' => $minAgeValue,
                    'max_age' => $maxAgeValue,
                    'image' => $imagePath,
                    'sort_order' => $sortOrderValue
                ]);

                $_SESSION['age_group_success'] =
                    'Age group created successfully.';

                header('Location: ' . BASE_URL . '/admin/age_groups.php');
                exit;

            } catch (PDOException $e) {
                error_log($e->getMessage());

                if ($imagePath !== null && file_exists(__DIR__ . '/../' . $imagePath)) {
                    unlink(__DIR__ . '/../' . $imagePath);
                }

                $errors[] = 'Unable to create the age group. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add Age Group';
$activePage = 'age_groups';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="admin-page-header">
    <div>
        <h2>Add Age Group</h2>
        <p>Create a new age group for your store.</p>
    </div>

    <a
        href="<?= BASE_URL ?>/admin/age_groups.php"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back to Age Groups
    </a>
</div>

<div class="admin-card">
    <div class="p-4">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form
            action=""
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="mb-3">
                <label for="name" class="form-label">
                    Age Group Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($name) ?>"
                    placeholder="e.g. Toddlers"
                >
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">
                    Slug
                </label>

                <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="form-control"
                    maxlength="100"
                    required
                    pattern="[a-z0-9]+(-[a-z0-9]+)*"
                    value="<?= htmlspecialchars($slug) ?>"
                    placeholder="e.g. toddlers"
                >

                <div class="form-text">
                    Use lowercase letters, numbers, and single hyphens.
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    maxlength="255"
                    rows="3"
                    required
                ><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label for="min_age" class="form-label">
                        Minimum Age
                    </label>

                    <input
                        type="number"
                        id="min_age"
                        name="min_age"
                        class="form-control"
                        min="0"
                        step="1"
                        required
                        value="<?= htmlspecialchars($minAge) ?>"
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label for="max_age" class="form-label">
                        Maximum Age
                    </label>

                    <input
                        type="number"
                        id="max_age"
                        name="max_age"
                        class="form-control"
                        min="0"
                        step="1"
                        required
                        value="<?= htmlspecialchars($maxAge) ?>"
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label for="sort_order" class="form-label">
                        Display Order
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        class="form-control"
                        min="0"
                        step="1"
                        required
                        value="<?= htmlspecialchars($sortOrder) ?>"
                    >
                </div>

            </div>

            <div class="mb-4">
                <label for="image" class="form-label">
                    Age Group Image (optional)
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <div class="form-text">
                    JPG, PNG, or WEBP. Maximum size: 2 MB.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2"></i>
                    Create Age Group
                </button>

                <a
                    href="<?= BASE_URL ?>/admin/age_groups.php"
                    class="btn btn-light"
                >
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
