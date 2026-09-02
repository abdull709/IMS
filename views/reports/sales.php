<section class="report-heading">
    <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
    <p>Sales Report <?= e($filters['start_date'] ?: 'Beginning') ?> to <?= e($filters['end_date'] ?: 'Today') ?> - Generated <?= e(format_date(date('Y-m-d H:i:s'))) ?></p>
</section>

<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('reports/sales')) ?>">
        <input type="hidden" name="route" value="reports/sales">
        <input class="form-control" name="start_date" type="date" value="<?= e($filters['start_date']) ?>">
        <input class="form-control" name="end_date" type="date" value="<?= e($filters['end_date']) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
    </form>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="metric-grid">
    <article class="metric-card">
        <i class="bi bi-receipt"></i>
        <span>Total Transactions</span>
        <strong><?= number_format((int) $summary['transactions']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-cash-stack"></i>
        <span>Total Sales</span>
        <strong><?= money($summary['total_sales']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-calculator"></i>
        <span>Average Transaction</span>
        <strong><?= money($summary['average_transaction']) ?></strong>
    </article>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Date</th><th>Invoice</th><th>Number of Items</th><th>User</th><th>Total Amount</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e(format_date($row['sale_day'], 'd M Y')) ?></td>
                        <td><?= e($row['invoice_number']) ?></td>
                        <td><?= number_format((int) $row['item_count']) ?></td>
                        <td><?= e($row['user_name']) ?></td>
                        <td><?= money($row['total_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No sales found for this date range.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
