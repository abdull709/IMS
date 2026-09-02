<?php
    $isEdit = is_array($category);
    $value = static fn(string $key, mixed $default = '') => old($key, $category[$key] ?? $default);
?>

<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('categories')) ?>"><i class="bi bi-arrow-left"></i> Back</a>
</section>

<section class="panel">
    <form method="post" action="<?= e($isEdit ? url('categories/update') : url('categories/store')) ?>" novalidate>
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="category_name">Category Name <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['category_name']) ? 'is-invalid' : '' ?>" id="category_name" name="category_name" value="<?= e($value('category_name')) ?>" required>
                <?php if (isset($errors['category_name'])): ?><div class="invalid-feedback"><?= e($errors['category_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="active" <?= selected($value('status', 'active'), 'active') ?>>Active</option>
                    <option value="inactive" <?= selected($value('status', 'active'), 'inactive') ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= e($value('description')) ?></textarea>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Save Category</button>
        </div>
    </form>
</section>
