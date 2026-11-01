
<?php

require_once __DIR__ . '/quick_actions.php';
recordAdminPageVisit($pdo);

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

    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/kiddos-icon.png">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">


    <link href="<?= BASE_URL ?>/assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/admin-polish.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin-polish.css') ?>" rel="stylesheet">
    <script src="<?= BASE_URL ?>/assets/js/admin-navigation.js?v=<?= filemtime(__DIR__ . '/../../assets/js/admin-navigation.js') ?>" defer></script>
</head>

<body class="admin-body">

    <div class="admin-wrapper">

        <?php require __DIR__ . '/sidebar.php'; ?>
        <button class="admin-drawer-overlay" type="button" aria-label="Close navigation" tabindex="-1" hidden></button>

        <div class="admin-main">

            <button class="admin-nav-toggle <?= $activePage === 'dashboard' ? 'dashboard-menu-toggle' : 'management-menu-toggle' ?>" type="button" aria-label="Open navigation" aria-controls="admin-navigation" aria-expanded="false"><i class="bi bi-list" aria-hidden="true"></i></button>

            <main class="admin-content">
