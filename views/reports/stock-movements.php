<section class="report-heading">
    <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
    <p>Stock Movement Report - Generated <?= e(format_date(date('Y-m-d H:i:s'))) ?></p>
</section>

<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('reports/stock-movements')) ?>">
        <input type="hidden" name="route" value="reports/stock-movements">
        <select class="form-select" name="product_id">
            <option value="">All products</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= (int) $product['id'] ?>" <?= selected($filters['product_id'], $product['id']) ?>><?= e($product['product_code'] . ' - ' . $product['product_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select" name="movement_type">
            <option value="">All movement types</option>
            <?php foreach (['opening_stock', 'stock_in', 'sale', 'adjustment_in', 'adjustment_out'] as $type): ?>
                <option value="<?= e($type) ?>" <?= selected($filters['movement_type'], $type) ?>><?= e(ucwords(str_replace('_', ' ', $type))) ?></option>
            <?php endforeach; ?>
        </select>
        <input class="form-control" name="start_date" type="date" value="<?= e($filters['start_date']) ?>">
        <input class="form-control" name="end_date" type="date" value="<?= e($filters['end_date']) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
    </form>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Date</th><th>Product</th><th>Movement Type</th><th>Quantity</th><th>Before</th><th>After</th><th>Reference</th><th>User</th><th>Remarks</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e(format_date($row['created_at'])) ?></td>
                        <td><strong><?= e($row['product_name']) ?></strong><span class="d-block text-muted small"><?= e($row['product_code']) ?></span></td>
                        <td><?= e(ucwords(str_replace('_', ' ', $row['movement_type']))) ?></td>
                        <td><?= number_format((int) $row['quantity']) ?></td>
                        <td><?= number_format((int) $row['quantity_before']) ?></td>
                        <td><?= number_format((int) $row['quantity_after']) ?></td>
                        <td><?= e($row['reference_type'] ?: '-') ?> #<?= e((string) ($row['reference_id'] ?? '-')) ?></td>
                        <td><?= e($row['user_name'] ?? '-') ?></td>
                        <td><?= e($row['remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No stock movements found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
