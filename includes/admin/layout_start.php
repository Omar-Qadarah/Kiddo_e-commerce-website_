
<?php

$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($pageTitle) ?> |
        <?= htmlspecialchars(APP_NAME) ?> Admin
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">


    <link href="<?= BASE_URL ?>/assets/css/admin.css" rel="stylesheet">
</head>

<body class="admin-body">

    <div class="admin-wrapper">

        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="admin-main">

            <?php require __DIR__ . '/navbar.php'; ?>

            <main class="admin-content">
