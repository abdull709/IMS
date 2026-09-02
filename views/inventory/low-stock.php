<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('inventory/low-stock')) ?>">
        <input type="hidden" name="route" value="inventory/low-stock">
        <input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="Search low stock products">
        <select class="form-select" name="stock">
            <option value="low" <?= selected($filters['stock'], 'low') ?>>Low and out of stock</option>
            <option value="out" <?= selected($filters['stock'], 'out') ?>>Out of stock only</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <?php if (is_admin()): ?>
        <a class="btn btn-primary" href="<?= e(url('inventory')) ?>"><i class="bi bi-plus-circle"></i> Add Stock</a>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Current Quantity</th><th>Reorder Level</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?= (($page - 1) * 12) + $index + 1 ?></td>
                        <td><strong><?= e($product['product_name']) ?></strong><span class="d-block text-muted small"><?= e($product['product_code']) ?></span></td>
                        <td><?= e($product['category_name']) ?></td>
                        <td><?= number_format((int) $product['quantity']) ?> <?= e($product['unit']) ?></td>
                        <td><?= number_format((int) $product['reorder_level']) ?></td>
                        <td><?= badge_stock((int) $product['quantity'], (int) $product['reorder_level']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No matching low-stock products.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'inventory/low-stock', $filters) ?></div>
</section>
