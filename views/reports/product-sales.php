<section class="report-heading">
    <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
    <p>Product Sales Report <?= e($filters['start_date'] ?: 'Beginning') ?> to <?= e($filters['end_date'] ?: 'Today') ?> - Generated <?= e(format_date(date('Y-m-d H:i:s'))) ?></p>
</section>

<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('reports/product-sales')) ?>">
        <input type="hidden" name="route" value="reports/product-sales">
        <input class="form-control" name="start_date" type="date" value="<?= e($filters['start_date']) ?>">
        <input class="form-control" name="end_date" type="date" value="<?= e($filters['end_date']) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
    </form>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Product</th><th>Product Code</th><th>Category</th><th>Quantity Sold</th><th>Sales Amount</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><strong><?= e($row['product_name']) ?></strong></td>
                        <td><?= e($row['product_code']) ?></td>
                        <td><?= e($row['category_name']) ?></td>
                        <td><?= number_format((int) $row['quantity_sold']) ?></td>
                        <td><?= money($row['sales_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No product sales found for this date range.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
