<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('inventory')) ?>">
        <input type="hidden" name="route" value="inventory">
        <input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="Search inventory">
        <select class="form-select" name="stock">
            <option value="">All stock</option>
            <option value="low" <?= selected($filters['stock'], 'low') ?>>Low stock</option>
            <option value="out" <?= selected($filters['stock'], 'out') ?>>Out of stock</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <a class="btn btn-outline-secondary" href="<?= e(url('inventory/movements')) ?>"><i class="bi bi-clock-history"></i> Movements</a>
</section>

<?php if (is_admin()): ?>
<section class="split-grid">
    <article class="panel">
        <div class="panel-title"><h2>Add Stock</h2></div>
        <form method="post" action="<?= e(url('inventory/stock-in')) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label" for="stock_product">Product</label>
                    <select class="form-select" id="stock_product" name="product_id" required>
                        <option value="">Select product</option>
                        <?php foreach ($allProducts as $product): ?>
                            <option value="<?= (int) $product['id'] ?>"><?= e($product['product_code'] . ' - ' . $product['product_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="stock_qty">Quantity to Add</label>
                    <input class="form-control" id="stock_qty" name="quantity" type="number" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="stock_remarks">Remarks</label>
                    <input class="form-control" id="stock_remarks" name="remarks" placeholder="Supplier delivery">
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-plus-circle"></i> Add Stock</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-title"><h2>Stock Adjustment</h2></div>
        <form method="post" action="<?= e(url('inventory/adjust')) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label" for="adjust_product">Product</label>
                    <select class="form-select" id="adjust_product" name="product_id" required>
                        <option value="">Select product</option>
                        <?php foreach ($allProducts as $product): ?>
                            <option value="<?= (int) $product['id'] ?>"><?= e($product['product_code'] . ' - ' . $product['product_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="adjustment_type">Type</label>
                    <select class="form-select" id="adjustment_type" name="adjustment_type">
                        <option value="increase">Increase</option>
                        <option value="decrease">Decrease</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="adjust_qty">Quantity</label>
                    <input class="form-control" id="adjust_qty" name="quantity" type="number" min="1" required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="reason">Reason</label>
                    <input class="form-control" id="reason" name="reason" placeholder="Physical count correction">
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-sliders"></i> Save Adjustment</button>
        </form>
    </article>
</section>
<?php endif; ?>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Quantity</th><th>Reorder Level</th><th>Stock Status</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?= (($page - 1) * 12) + $index + 1 ?></td>
                        <td><strong><?= e($product['product_name']) ?></strong><span class="d-block text-muted small"><?= e($product['product_code']) ?></span></td>
                        <td><?= e($product['category_name']) ?></td>
                        <td><?= number_format((int) $product['quantity']) ?> <?= e($product['unit']) ?></td>
                        <td><?= number_format((int) $product['reorder_level']) ?></td>
                        <td><?= badge_stock((int) $product['quantity'], (int) $product['reorder_level']) ?></td>
                        <td><?= badge_status($product['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No inventory records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'inventory', $filters) ?></div>
</section>
