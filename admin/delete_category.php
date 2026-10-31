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

if (!$id || $id < 1) {
    $_SESSION['category_error'] = 'Invalid category ID.';
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

$categoryModel = new Category($pdo);

try {

    $category = $categoryModel->getById($id);

    if ($category === false) {
        $_SESSION['category_error'] = 'Category not found.';
    } else {

        $deleted = $categoryModel->delete($id);

        if ($deleted) {
            CategoryImage::delete($category['image'] ?? null);
            $_SESSION['category_success'] = 'Category deleted successfully.';
        } else {
            $_SESSION['category_error'] =
                'This category cannot be deleted because it has products, or it no longer exists.';
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());

    $_SESSION['category_error'] =
        'Unable to delete this category. Please try again.';
}

header('Location: ' . BASE_URL . '/admin/categories.php');
exit;
