<section class="report-heading">
    <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
    <p>Inventory Report - Generated <?= e(format_date(date('Y-m-d H:i:s'))) ?></p>
</section>

<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('reports/inventory')) ?>">
        <input type="hidden" name="route" value="reports/inventory">
        <input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="Search product, code, category">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="panel">
    <?php $totalValue = array_sum(array_map(static fn($row) => (float) $row['stock_value'], $rows)); ?>
    <div class="summary-row mb-3"><span>Total Stock Value</span><strong><?= money($totalValue) ?></strong></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Product Code</th><th>Product</th><th>Category</th><th>Cost Price</th><th>Selling Price</th><th>Quantity</th><th>Reorder Level</th><th>Stock Status</th><th>Stock Value</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['product_code']) ?></td>
                        <td><strong><?= e($row['product_name']) ?></strong></td>
                        <td><?= e($row['category_name']) ?></td>
                        <td><?= money($row['cost_price']) ?></td>
                        <td><?= money($row['selling_price']) ?></td>
                        <td><?= number_format((int) $row['quantity']) ?> <?= e($row['unit']) ?></td>
                        <td><?= number_format((int) $row['reorder_level']) ?></td>
                        <td><?= badge_stock((int) $row['quantity'], (int) $row['reorder_level']) ?></td>
                        <td><?= money($row['stock_value']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No inventory records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
