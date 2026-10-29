
<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Category.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$name = trim($_POST['name'] ?? '');
$slug = strtolower(trim($_POST['slug'] ?? ''));
$description = trim($_POST['description'] ?? '');
$icon = trim($_POST['icon'] ?? '');

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

try {
    $categoryModel->update($id, [
        'name' => $name,
        'slug' => $slug,
        'description' => $description !== '' ? $description : null,
        'icon' => $icon !== '' ? $icon : null
    ]);

    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Unable to update the category. Please try again.');
}
