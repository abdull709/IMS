<?php
    $isEdit = is_array($user);
    $value = static fn(string $key, mixed $default = '') => old($key, $user[$key] ?? $default);
?>

<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('users')) ?>"><i class="bi bi-arrow-left"></i> Back</a>
</section>

<section class="panel">
    <form method="post" action="<?= e($isEdit ? url('users/update') : url('users/store')) ?>" novalidate>
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= e($value('full_name')) ?>" required>
                <?php if (isset($errors['full_name'])): ?><div class="invalid-feedback"><?= e($errors['full_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= e($value('username')) ?>" required>
                <?php if (isset($errors['username'])): ?><div class="invalid-feedback"><?= e($errors['username']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" type="email" value="<?= e($value('email')) ?>">
                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="role">Role</label>
                <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role">
                    <option value="staff" <?= selected($value('role', 'staff'), 'staff') ?>>Staff</option>
                    <option value="admin" <?= selected($value('role', 'staff'), 'admin') ?>>Admin</option>
                </select>
                <?php if (isset($errors['role'])): ?><div class="invalid-feedback"><?= e($errors['role']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="status">Status</label>
                <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status">
                    <option value="active" <?= selected($value('status', 'active'), 'active') ?>>Active</option>
                    <option value="inactive" <?= selected($value('status', 'active'), 'inactive') ?>>Inactive</option>
                </select>
                <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= e($errors['status']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password"><?= $isEdit ? 'New Password' : 'Password' ?> <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?></label>
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" type="password" <?= $isEdit ? '' : 'required' ?>>
                <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" type="password" <?= $isEdit ? '' : 'required' ?>>
                <?php if (isset($errors['confirm_password'])): ?><div class="invalid-feedback"><?= e($errors['confirm_password']) ?></div><?php endif; ?>
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="alert alert-light border mt-4">Leave the password fields blank to keep the current password.</div>
        <?php endif; ?>

        <button class="btn btn-primary mt-4" type="submit"><i class="bi bi-save"></i> Save User</button>
    </form>
</section>
