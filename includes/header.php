<?php
// Storefront layout start. Every customer page includes this after setting
// $pageTitle and $activePage (same convention as the admin layout).
require_once __DIR__ . '/init.php';

$pageTitle  = $pageTitle ?? 'Toys that grow little minds';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Kiddo's</title>
    <meta name="description" content="Kiddo's is a toy store in Jordan for kids aged 3 to 12. Pay cash on delivery.">

    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/kiddos-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@500;600;700;800&display=swap" rel="stylesheet">

        <link href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>" rel="stylesheet">
        <link href="<?= BASE_URL ?>/assets/css/assistant.css?v=<?= filemtime(__DIR__ . '/../assets/css/assistant.css') ?>" rel="stylesheet">
<?php if (in_array($activePage, ['login', 'register', 'logout'], true)): ?>
    <link href="<?= BASE_URL ?>/assets/css/auth.css" rel="stylesheet">
<?php endif; ?>
</head>
<body>

<?php require __DIR__ . '/navbar.php'; ?>

<main>
