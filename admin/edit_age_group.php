<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { checkCsrf(); }
require_once __DIR__ . '/../classes/AgeGroup.php';

$ageGroupModel = new AgeGroup($pdo);
$errors = [];


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['age_group_error'] = 'Invalid age group ID.';
    header('Location: ' . BASE_URL . '/admin/age_groups.php');
    exit;
}

$ageGroup = $ageGroupModel->getById($id);

if (!$ageGroup) {
    $_SESSION['age_group_error'] = 'Age group not found.';
    header('Location: ' . BASE_URL . '/admin/age_groups.php');
    exit;
}


$name = $ageGroup['name'];
$slug = $ageGroup['slug'];
$description = $ageGroup['description'];
$minAge = (string) $ageGroup['min_age'];
$maxAge = (string) $ageGroup['max_age'];
$sortOrder = (string) $ageGroup['sort_order'];
$imagePath = $ageGroup['image'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(trim($_POST['slug'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $minAge = trim($_POST['min_age'] ?? '');
    $maxAge = trim($_POST['max_age'] ?? '');
    $sortOrder = trim($_POST['sort_order'] ?? '0');


    if ($name === '') {
        $errors[] = 'Name is required.';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Name must not exceed 100 characters.';
    }

    if ($slug === '') {
        $errors[] = 'Slug is required.';
    } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        $errors[] = 'Slug must contain lowercase letters, numbers, and single hyphens only.';
    } elseif ($ageGroupModel->slugExists($slug, $id)) {
        $errors[] = 'This slug is already used by another age group.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    } elseif (mb_strlen($description) > 255) {
        $errors[] = 'Description must not exceed 255 characters.';
    }

    $minAgeValue = filter_var($minAge, FILTER_VALIDATE_INT);

    if ($minAge === '' || $minAgeValue === false || $minAgeValue < 0) {
        $errors[] = 'Minimum age must be a non-negative whole number.';
    }

    $maxAgeValue = filter_var($maxAge, FILTER_VALIDATE_INT);

    if ($maxAge === '' || $maxAgeValue === false || $maxAgeValue < 0) {
        $errors[] = 'Maximum age must be a non-negative whole number.';
    }

    if (
        $minAgeValue !== false &&
        $maxAgeValue !== false &&
        $minAgeValue >= 0 &&
        $maxAgeValue >= 0 &&
        $minAgeValue > $maxAgeValue
    ) {
        $errors[] = 'Minimum age cannot be greater than maximum age.';
    }

    $sortOrderValue = filter_var($sortOrder, FILTER_VALIDATE_INT);

    if ($sortOrderValue === false || $sortOrderValue < 0) {
        $errors[] = 'Sort order must be a non-negative whole number.';
    }

    $newImagePath = $imagePath;
    $uploadedImagePath = null;
    $imageExtension = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'The image upload failed.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image size must not exceed 2 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['image']['tmp_name']);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedTypes[$mimeType])) {
                $errors[] = 'Only JPG, PNG, and WEBP images are allowed.';
            } else {
                $imageExtension = $allowedTypes[$mimeType];
            }
        }
    }

    if (empty($errors) && $imageExtension !== null) {
        $uploadDirectory = __DIR__ . '/../uploads/age-groups/';

        if (
            !is_dir($uploadDirectory) &&
            !mkdir($uploadDirectory, 0755, true) &&
            !is_dir($uploadDirectory)
        ) {
            $errors[] = 'Could not create the image upload directory.';
        } else {
            $imageName = bin2hex(random_bytes(16)) . '.' . $imageExtension;
            $destination = $uploadDirectory . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $newImagePath = 'uploads/age-groups/' . $imageName;
                $uploadedImagePath = $destination;
            } else {
                $errors[] = 'Could not save the uploaded image.';
            }
        }
    }

    if (empty($errors)) {
        try {
            $updated = $ageGroupModel->update($id, [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'min_age' => $minAgeValue,
                'max_age' => $maxAgeValue,
                'image' => $newImagePath,
                'sort_order' => $sortOrderValue
            ]);

            if ($updated) {
                if ($uploadedImagePath !== null && !empty($imagePath)) {
                    $oldImageFile = __DIR__ . '/../' . $imagePath;

                    if (is_file($oldImageFile)) {
                        unlink($oldImageFile);
                    }
                }

                $_SESSION['age_group_success'] = 'Age group updated successfully.';
                header('Location: ' . BASE_URL . '/admin/age_groups.php');
                exit;
            }

            if ($uploadedImagePath !== null && is_file($uploadedImagePath)) {
                unlink($uploadedImagePath);
            }

            $errors[] = 'No changes were saved. The age group may already have these values.';
        } catch (PDOException $e) {
            error_log($e->getMessage());

            if ($uploadedImagePath !== null && is_file($uploadedImagePath)) {
                unlink($uploadedImagePath);
            }

            $errors[] = 'A database error occurred. Please try again.';
        }
    }
}

$pageTitle = 'Edit Age Group';
$activePage = 'age_groups';

require_once __DIR__ . '/../includes/admin/layout_start.php';
?>

<div class="admin-page-header">
    <div>
        <h1>Edit Age Group</h1>
        <p>Update the age group information.</p>
    </div>

    <a href="<?= BASE_URL ?>/admin/age_groups.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Age Groups
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-card p-4">
    <form method="POST"
          action="<?= BASE_URL ?>
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">/admin/edit_age_group.php?id=<?= (int) $id ?>"
          enctype="multipart/form-data">

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control"
                   maxlength="100" required
                   value="<?= htmlspecialchars($name) ?>">
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-control"
                   required
                   pattern="[a-z0-9]+(-[a-z0-9]+)*"
                   value="<?= htmlspecialchars($slug) ?>">
            <div class="form-text">Use lowercase letters, numbers, and hyphens.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"
                      rows="3" maxlength="255" required><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="min_age" class="form-label">Minimum Age</label>
                <input type="number" id="min_age" name="min_age"
                       class="form-control" min="0" required
                       value="<?= htmlspecialchars($minAge) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label for="max_age" class="form-label">Maximum Age</label>
                <input type="number" id="max_age" name="max_age"
                       class="form-control" min="0" required
                       value="<?= htmlspecialchars($maxAge) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order"
                       class="form-control" min="0" required
                       value="<?= htmlspecialchars($sortOrder) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Image</label><br>

            <?php if (!empty($imagePath)): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($imagePath) ?>"
                     alt="<?= htmlspecialchars($name) ?>"
                     width="120" height="120"
                     style="object-fit: cover; border-radius: 8px;">
            <?php else: ?>
                <p class="text-secondary">No image uploaded.</p>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="image" class="form-label">Replace Image (Optional)</label>
            <input type="file" id="image" name="image" class="form-control"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <div class="form-text">
                Leave empty to keep the current image. Maximum size: 2 MB.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Save Changes
        </button>

        <a href="<?= BASE_URL ?>/admin/age_groups.php"
           class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/admin/layout_end.php'; ?>
