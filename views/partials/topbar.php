<header class="topbar">
    <button class="icon-button d-lg-none" type="button" data-sidebar-toggle aria-label="Toggle navigation">
        <i class="bi bi-list"></i>
    </button>
    <div>
        <h1><?= e($title ?? 'Dashboard') ?></h1>
        <span class="topbar-subtitle"><?= e(setting('business_name', 'Local Business')) ?></span>
    </div>
    <div class="topbar-actions">
        <a class="notification-dot" href="<?= e(url('inventory/low-stock')) ?>" title="Low stock products">
            <i class="bi bi-bell"></i>
        </a>
        <div class="user-chip">
            <span><?= e($user['full_name']) ?></span>
            <small><?= e(ucfirst($user['role'])) ?></small>
        </div>
    </div>
</header>
