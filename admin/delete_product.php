<?php

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    $product = new Product($pdo);
    $product->delete($id);
}

header('Location: products.php');
exit;