<?php

require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../config/Database.php';


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$database = new Database();
$pdo = $database->connect();
