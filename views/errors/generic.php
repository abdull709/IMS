<section class="empty-state">
    <div class="empty-icon"><i class="bi bi-shield-exclamation"></i></div>
    <h2><?= e($code ?? 'Error') ?> - <?= e($title ?? 'Error') ?></h2>
    <p><?= e($message ?? 'Something went wrong.') ?></p>
    <?php if (is_logged_in()): ?>
        <a class="btn btn-primary" href="<?= e(url('dashboard')) ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <?php else: ?>
        <a class="btn btn-primary" href="<?= e(url('login')) ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a>
    <?php endif; ?>
</section>
