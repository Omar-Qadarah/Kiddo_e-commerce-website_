<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
header('Content-Type: application/json; charset=utf-8');
$query = is_string($_GET['q'] ?? null) ? mb_substr(preg_replace('/\s+/u', ' ', trim($_GET['q'])),0,60) : '';
if (mb_strlen($query) < 2) { echo '[]'; exit; }
$catalog = new ToyCatalog($pdo);
$items = $catalog->getFilteredToys(['q'=>$query],5);
echo json_encode(array_map(fn($toy) => ['name'=>$toy['name'],'url'=>BASE_URL.'/products/details.php?id='.(int)$toy['id']],$items));
