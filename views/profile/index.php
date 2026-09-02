<section class="split-grid">
    <article class="panel">
        <div class="panel-title"><h2>Profile Information</h2></div>
        <form method="post" action="<?= e(url('profile/update')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= e($user['full_name']) ?>">
                    <?php if (isset($errors['full_name'])): ?><div class="invalid-feedback"><?= e($errors['full_name']) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" type="email" value="<?= e($user['email']) ?>">
                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary mt-4" type="submit"><i class="bi bi-save"></i> Update Profile</button>
        </form>

        <hr class="my-4">
        <div class="row g-3">
            <div class="col-md-6"><span class="text-muted d-block">Username</span><strong><?= e($user['username']) ?></strong></div>
            <div class="col-md-6"><span class="text-muted d-block">Role</span><strong><?= e(ucfirst($user['role'])) ?></strong></div>
            <div class="col-md-6"><span class="text-muted d-block">Last Login</span><strong><?= e(format_date($user['last_login'])) ?></strong></div>
            <div class="col-md-6"><span class="text-muted d-block">Account Created</span><strong><?= e(format_date($user['created_at'])) ?></strong></div>
        </div>
    </article>

    <article class="panel">
        <div class="panel-title"><h2>Change Password</h2></div>
        <form method="post" action="<?= e(url('profile/password')) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="current_password">Current Password</label>
                <input class="form-control" id="current_password" name="current_password" type="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="new_password">New Password</label>
                <input class="form-control" id="new_password" name="new_password" type="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <input class="form-control" id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="bi bi-shield-lock"></i> Change Password</button>
        </form>
    </article>
</section>
