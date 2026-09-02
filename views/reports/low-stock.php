<section class="report-heading">
    <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
    <p>Low Stock Report - Generated <?= e(format_date(date('Y-m-d H:i:s'))) ?></p>
</section>

<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('reports')) ?>"><i class="bi bi-arrow-left"></i> Reports</a>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Product</th><th>Product Code</th><th>Category</th><th>Current Quantity</th><th>Reorder Level</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><strong><?= e($row['product_name']) ?></strong></td>
                        <td><?= e($row['product_code']) ?></td>
                        <td><?= e($row['category_name']) ?></td>
                        <td><?= number_format((int) $row['quantity']) ?> <?= e($row['unit']) ?></td>
                        <td><?= number_format((int) $row['reorder_level']) ?></td>
                        <td><?= badge_stock((int) $row['quantity'], (int) $row['reorder_level']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No low-stock products.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
