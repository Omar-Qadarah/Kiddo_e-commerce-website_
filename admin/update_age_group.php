<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/AgeGroup.php';

$ageGroupModel = new AgeGroup($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/age_groups.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

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

$name = trim($_POST['name'] ?? '');
$slug = strtolower(trim($_POST['slug'] ?? ''));
$description = trim($_POST['description'] ?? '');
$minAge = trim($_POST['min_age'] ?? '');
$maxAge = trim($_POST['max_age'] ?? '');
$sortOrder = trim($_POST['sort_order'] ?? '0');

$errors = [];

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

$newImagePath = $ageGroup['image'];
$uploadedImagePath = null;
$imageExtension = null;

if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'The image upload failed.';
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Image size must not exceed 2 MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($_FILES['image']['tmp_name']);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedTypes[$mimeType])) {
            $errors[] = 'Only JPG, PNG, and WEBP images are allowed.';
        } else {
            $imageExtension = $allowedTypes[$mimeType];
        }
    }
}

if (!empty($errors)) {
    $_SESSION['age_group_edit_errors'][$id] = $errors;

    $_SESSION['age_group_edit_old'][$id] = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'min_age' => $minAge,
        'max_age' => $maxAge,
        'sort_order' => $sortOrder
    ];

    header('Location: ' . BASE_URL . '/admin/age_groups.php?edit=' . $id);
    exit;
}

if ($imageExtension !== null) {
    $uploadDirectory = __DIR__ . '/../uploads/age-groups/';

    if (
        !is_dir($uploadDirectory) &&
        !mkdir($uploadDirectory, 0755, true) &&
        !is_dir($uploadDirectory)
    ) {
        $_SESSION['age_group_edit_errors'][$id] = [
            'Could not create the image upload directory.'
        ];

        $_SESSION['age_group_edit_old'][$id] = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'min_age' => $minAge,
            'max_age' => $maxAge,
            'sort_order' => $sortOrder
        ];

        header('Location: ' . BASE_URL . '/admin/age_groups.php?edit=' . $id);
        exit;
    }

    $imageName = bin2hex(random_bytes(16)) . '.' . $imageExtension;
    $destination = $uploadDirectory . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        $_SESSION['age_group_edit_errors'][$id] = [
            'Could not save the uploaded image.'
        ];

        $_SESSION['age_group_edit_old'][$id] = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'min_age' => $minAge,
            'max_age' => $maxAge,
            'sort_order' => $sortOrder
        ];

        header('Location: ' . BASE_URL . '/admin/age_groups.php?edit=' . $id);
        exit;
    }

    $newImagePath = 'uploads/age-groups/' . $imageName;
    $uploadedImagePath = $destination;
}

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

    if (!$updated) {
        if ($uploadedImagePath !== null && is_file($uploadedImagePath)) {
            unlink($uploadedImagePath);
        }

        $_SESSION['age_group_success'] = 'No changes were needed; the age group already has these values.';
        header('Location: ' . BASE_URL . '/admin/age_groups.php');
        exit;
    }

    // Delete the old image only after a successful database update
    if ($uploadedImagePath !== null && !empty($ageGroup['image'])) {
        $oldImageFile = __DIR__ . '/../' . $ageGroup['image'];

        if (is_file($oldImageFile)) {
            unlink($oldImageFile);
        }
    }

    unset(
        $_SESSION['age_group_edit_errors'][$id],
        $_SESSION['age_group_edit_old'][$id]
    );

    $_SESSION['age_group_success'] = 'Age group updated successfully.';

    header('Location: ' . BASE_URL . '/admin/age_groups.php');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    if ($uploadedImagePath !== null && is_file($uploadedImagePath)) {
        unlink($uploadedImagePath);
    }

    $_SESSION['age_group_edit_errors'][$id] = [
        'A database error occurred. Please try again.'
    ];

    $_SESSION['age_group_edit_old'][$id] = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'min_age' => $minAge,
        'max_age' => $maxAge,
        'sort_order' => $sortOrder
    ];

    header('Location: ' . BASE_URL . '/admin/age_groups.php?edit=' . $id);
    exit;
}
