<?php
// Storefront layout start for the landing, toys and toy details pages.
// Same <head> as includes/header.php, but it loads the new navbar
// (includes/shop_navbar.php) so the shared header.php/navbar.php stay as they are.
// The page closes the layout with the existing includes/footer.php.
//
// Before including it, a page may set:
//   $pageTitle    text for <title>
//   $activePage   home | categories | toys  (highlights the nav link)
//   $pageStyles   extra CSS files from assets/css/ (or full URLs)
//   $pageScripts  extra JS files from assets/js/
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/shop_functions.php';

$pageTitle   = $pageTitle ?? 'Toys that grow little minds';
$activePage  = $activePage ?? '';
$pageStyles  = $pageStyles ?? [];
$pageScripts = $pageScripts ?? [];

// Adds ?v=<last change time> so the browser picks up edited files.
function shopAsset(string $folder, string $file): string
{
    if (preg_match('#^https?://#i', $file)) {
        return $file;
    }
    $diskPath = __DIR__ . '/../assets/' . $folder . '/' . $file;
    $version  = is_file($diskPath) ? filemtime($diskPath) : 1;

    return BASE_URL . '/assets/' . $folder . '/' . $file . '?v=' . $version;
}
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

    <link href="<?= shopAsset('css', 'style.css') ?>" rel="stylesheet">
    <link href="<?= shopAsset('css', 'storefront.css') ?>" rel="stylesheet">
    <link href="<?= shopAsset('css', 'assistant.css') ?>" rel="stylesheet">
<?php foreach ($pageStyles as $style): ?>
    <link href="<?= htmlspecialchars(shopAsset('css', $style)) ?>" rel="stylesheet">
<?php endforeach; ?>

    <script src="<?= shopAsset('js', 'storefront.js') ?>" defer></script>
<?php foreach ($pageScripts as $script): ?>
    <script src="<?= htmlspecialchars(shopAsset('js', $script)) ?>" defer></script>
<?php endforeach; ?>
</head>
<body>

<?php require __DIR__ . '/shop_navbar.php'; ?>

<main>
