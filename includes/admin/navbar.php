
<header class="admin-navbar">
    <button class="admin-nav-toggle" type="button" aria-label="Open navigation" aria-controls="admin-navigation" aria-expanded="false"><i class="bi bi-list" aria-hidden="true"></i></button>
    <div class="navbar-page-info">
        <span class="navbar-eyebrow">KIDDO'S ADMINISTRATION</span>
    </div>

    <div class="navbar-user">
        <div class="user-avatar">
            <i class="bi bi-person-fill"></i>
        </div>

        <div class="user-details">
            <span class="user-name">
                <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
            </span>
            <span class="user-role">Administrator</span>
        </div>
    </div>
</header>
