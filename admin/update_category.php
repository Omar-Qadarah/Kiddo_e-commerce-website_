
<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/CategoryImage.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}
checkCsrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$name = trim($_POST['name'] ?? '');
$slug = strtolower(trim($_POST['slug'] ?? ''));
$description = trim($_POST['description'] ?? '');

if (!$id || $id < 1 || $name === '' || $slug === '') {
    exit('Invalid category data.');
}

if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    exit('Invalid slug format.');
}

$categoryModel = new Category($pdo);

$existingCategory = $categoryModel->getById($id);

if ($existingCategory === false) {
    exit('Category not found.');
}

if ($categoryModel->nameExists($name, $id)) {
    exit('This category name already exists.');
}

if ($categoryModel->slugExists($slug, $id)) {
    exit('This category slug already exists.');
}

$uploadedImage = null;
try {
    $uploadedImage = CategoryImage::upload($_FILES['image'] ?? null);
    $imagePath = $uploadedImage !== null
        ? $uploadedImage
        : (!empty($_POST['remove_image']) ? null : ($existingCategory['image'] ?? null));

    $updated = $categoryModel->update($id, [
        'name' => $name,
        'slug' => $slug,
        'description' => $description !== '' ? $description : null,
        'icon' => $existingCategory['icon'] ?? null,
        'image' => $imagePath,
    ]);
    if (!$updated) {
        throw new RuntimeException('Unable to update the category.');
    }

    if (!empty($existingCategory['image']) && $existingCategory['image'] !== $imagePath) {
        CategoryImage::delete($existingCategory['image']);
    }

    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
} catch (Throwable $e) {
    CategoryImage::delete($uploadedImage);
    error_log($e->getMessage());
    $_SESSION['category_error'] = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to update the category. Please try again.';
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}
