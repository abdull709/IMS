<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('products')) ?>"><i class="bi bi-arrow-left"></i> Back</a>
    <?php if (is_admin()): ?>
        <a class="btn btn-primary" href="<?= e(url('products/edit', ['id' => $product['id']])) ?>"><i class="bi bi-pencil"></i> Edit Product</a>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-title">
        <h2><?= e($product['product_name']) ?></h2>
        <div><?= badge_stock((int) $product['quantity'], (int) $product['reorder_level']) ?> <?= badge_status($product['status']) ?></div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <span class="text-muted d-block">Product Code</span>
            <strong><?= e($product['product_code']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Category</span>
            <strong><?= e($product['category_name']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Unit</span>
            <strong><?= e($product['unit']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Cost Price</span>
            <strong><?= money($product['cost_price']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Selling Price</span>
            <strong><?= money($product['selling_price']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Quantity / Reorder Level</span>
            <strong><?= (int) $product['quantity'] ?> / <?= (int) $product['reorder_level'] ?></strong>
        </div>
        <div class="col-12">
            <span class="text-muted d-block">Description</span>
            <p class="mb-0"><?= e($product['description'] ?: 'No description recorded.') ?></p>
        </div>
    </div>
</section>
