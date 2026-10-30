<?php
require_once __DIR__ . '/account_helpers.php';

$accountUserId = (int)($_SESSION['user_id'] ?? 0);
$accountUserName = trim((string)($_SESSION['user_name'] ?? ''));
$accountNameParts = preg_split('/\s+/u', $accountUserName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
$accountFirstName = $accountNameParts[0] ?? 'there';
$accountInitials = accountUserInitials($accountUserName);
$accountImageUrl = accountProfileImageUrl($accountUserId);
?>
<details class="account-menu">
    <summary class="account-menu__trigger" aria-label="Open account menu" title="My account">
        <span class="account-menu__avatar">
            <?php if ($accountImageUrl): ?>
                <img src="<?= htmlspecialchars($accountImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="">
            <?php else: ?>
                <span><?= htmlspecialchars($accountInitials, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </span>
        <svg class="account-menu__chevron" viewBox="0 0 20 20" aria-hidden="true"><path d="m5 7.5 5 5 5-5"/></svg>
    </summary>
    <div class="account-menu__dropdown">
        <div class="account-menu__heading">
            <strong><?= htmlspecialchars($accountUserName !== '' ? $accountUserName : $accountFirstName, ENT_QUOTES, 'UTF-8') ?></strong>
            <span>Your Kiddo account</span>
        </div>
        <a href="<?= BASE_URL ?>/auth/profile.php">My profile</a>
        <a href="<?= BASE_URL ?>/orders/index.php">My orders</a>
        <form action="<?= BASE_URL ?>/auth/logout.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button class="account-menu__logout" type="submit">Log out</button>
        </form>
    </div>
</details>
