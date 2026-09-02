<section class="auth-card">
    <div class="auth-logo"><i class="bi bi-box-seam"></i></div>
    <h1 class="h3 mb-1">Inventory Management System</h1>
    <p class="text-muted mb-4">Sign in to manage stock, sales, and reports.</p>

    <?php require VIEW_PATH . '/partials/flash.php'; ?>

    <form method="post" action="<?= e(url('login')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="login">Username or Email</label>
            <input class="form-control" id="login" name="login" value="<?= e(old('login')) ?>" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" id="password" name="password" type="password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
    </form>

    <div class="alert alert-light border mt-4 mb-0 small">
        <strong>Development login:</strong><br>
        Admin: <code>admin</code> / <code>password</code><br>
        Staff: <code>staff</code> / <code>password</code>
    </div>
</section>
