<?php
// init.php must be loaded first so the database connection and session exist.
require_once __DIR__ . '/../classes/AdminAccess.php';
$adminUserId = (new AdminAccess($pdo))->requireAdmin();
