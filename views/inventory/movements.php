<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('inventory/movements')) ?>">
        <input type="hidden" name="route" value="inventory/movements">
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
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Movement Type</th>
                    <th>Quantity</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movements as $movement): ?>
                    <tr>
                        <td><?= e(format_date($movement['created_at'])) ?></td>
                        <td><strong><?= e($movement['product_name']) ?></strong><span class="d-block text-muted small"><?= e($movement['product_code']) ?></span></td>
                        <td><span class="badge text-bg-secondary"><?= e(ucwords(str_replace('_', ' ', $movement['movement_type']))) ?></span></td>
                        <td><?= number_format((int) $movement['quantity']) ?></td>
                        <td><?= number_format((int) $movement['quantity_before']) ?></td>
                        <td><?= number_format((int) $movement['quantity_after']) ?></td>
                        <td><?= e($movement['reference_type'] ?: '-') ?> #<?= e((string) ($movement['reference_id'] ?? '-')) ?></td>
                        <td><?= e($movement['user_name'] ?? '-') ?></td>
                        <td><?= e($movement['remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$movements): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No stock movements found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'inventory/movements', $filters) ?></div>
</section>
