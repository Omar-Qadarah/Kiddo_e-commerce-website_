<?php
 
require_once __DIR__ . '/../classes/AdminAccess.php';
$adminUserId = (new AdminAccess($pdo))->requireAdmin();
