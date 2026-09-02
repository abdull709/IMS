<?php
    $isEdit = isset($product['id']);
    $value = static fn(string $key, mixed $default = '') => old($key, $product[$key] ?? $default);
?>

<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('products')) ?>"><i class="bi bi-arrow-left"></i> Back</a>
</section>

<section class="panel">
    <form method="post" action="<?= e($isEdit ? url('products/update') : url('products/store')) ?>" novalidate>
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="product_code">Product Code <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['product_code']) ? 'is-invalid' : '' ?>" id="product_code" name="product_code" value="<?= e($value('product_code')) ?>" required>
                <?php if (isset($errors['product_code'])): ?><div class="invalid-feedback"><?= e($errors['product_code']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-8">
                <label class="form-label" for="product_name">Product Name <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['product_name']) ? 'is-invalid' : '' ?>" id="product_name" name="product_name" value="<?= e($value('product_name')) ?>" required>
                <?php if (isset($errors['product_name'])): ?><div class="invalid-feedback"><?= e($errors['product_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" id="category_id" name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= selected($value('category_id'), $category['id']) ?>><?= e($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?><div class="invalid-feedback"><?= e($errors['category_id']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="cost_price">Cost Price</label>
                <input class="form-control <?= isset($errors['cost_price']) ? 'is-invalid' : '' ?>" id="cost_price" name="cost_price" type="number" min="0" step="0.01" value="<?= e($value('cost_price', '0.00')) ?>">
                <?php if (isset($errors['cost_price'])): ?><div class="invalid-feedback"><?= e($errors['cost_price']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="selling_price">Selling Price</label>
                <input class="form-control <?= isset($errors['selling_price']) ? 'is-invalid' : '' ?>" id="selling_price" name="selling_price" type="number" min="0" step="0.01" value="<?= e($value('selling_price', '0.00')) ?>">
                <?php if (isset($errors['selling_price'])): ?><div class="invalid-feedback"><?= e($errors['selling_price']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="quantity"><?= $isEdit ? 'Current Quantity' : 'Opening Quantity' ?></label>
                <input class="form-control <?= isset($errors['quantity']) ? 'is-invalid' : '' ?>" id="quantity" name="quantity" type="number" min="0" value="<?= e($value('quantity', 0)) ?>">
                <?php if (isset($errors['quantity'])): ?><div class="invalid-feedback"><?= e($errors['quantity']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="reorder_level">Reorder Level</label>
                <input class="form-control <?= isset($errors['reorder_level']) ? 'is-invalid' : '' ?>" id="reorder_level" name="reorder_level" type="number" min="0" value="<?= e($value('reorder_level', setting('low_stock_default', 5))) ?>">
                <?php if (isset($errors['reorder_level'])): ?><div class="invalid-feedback"><?= e($errors['reorder_level']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="unit">Unit</label>
                <input class="form-control" id="unit" name="unit" value="<?= e($value('unit', 'pcs')) ?>" placeholder="pcs, bottle, kg">
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

        <div class="alert alert-light border mt-4">
            Quantity edits are recorded as stock adjustment movements, preserving inventory history.
        </div>

        <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Save Product</button>
    </form>
</section>
