<section class="metric-grid">
    <article class="metric-card">
        <i class="bi bi-box"></i>
        <span>Total Products</span>
        <strong><?= number_format($stats['total_products']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-tags"></i>
        <span>Total Categories</span>
        <strong><?= number_format($stats['total_categories']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-stack"></i>
        <span>Current Stock Quantity</span>
        <strong><?= number_format($stats['current_stock']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-exclamation-triangle"></i>
        <span>Low Stock Products</span>
        <strong><?= number_format($stats['low_stock']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-cash-stack"></i>
        <span>Today's Sales</span>
        <strong><?= money($stats['today_sales']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-receipt"></i>
        <span>Today's Transactions</span>
        <strong><?= number_format($stats['today_transactions']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-calendar3"></i>
        <span>Monthly Sales</span>
        <strong><?= money($stats['monthly_sales']) ?></strong>
    </article>
    <article class="metric-card">
        <i class="bi bi-cart-plus"></i>
        <span>Quick Action</span>
        <a class="btn btn-sm btn-primary mt-2" href="<?= e(url('sales/create')) ?>"><i class="bi bi-plus-circle"></i> New Sale</a>
    </article>
</section>

<section class="split-grid">
    <div>
        <article class="panel">
            <div class="panel-title">
                <h2>Sales for the Last 7 Days</h2>
                <?php if (is_admin()): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('reports/sales')) ?>"><i class="bi bi-printer"></i> Report</a>
                <?php endif; ?>
            </div>
            <?php
                $max = max(array_map(static fn($row) => (float) $row['total'], $salesChart ?: [['total' => 0]]));
                $chartByDate = [];
                foreach ($salesChart as $row) {
                    $chartByDate[$row['sale_day']] = (float) $row['total'];
                }
            ?>
            <div class="chart-bars" aria-label="Sales chart">
                <?php for ($i = 6; $i >= 0; $i--): ?>
                    <?php
                        $date = date('Y-m-d', strtotime("-{$i} days"));
                        $total = $chartByDate[$date] ?? 0;
                        $height = $max > 0 ? max(3, (int) (($total / $max) * 150)) : 3;
                    ?>
                    <div class="chart-bar" title="<?= e(money($total)) ?>">
                        <span style="height: <?= $height ?>px"></span>
                        <small><?= e(date('D', strtotime($date))) ?></small>
                    </div>
                <?php endfor; ?>
            </div>
        </article>

        <article class="panel">
            <div class="panel-title">
                <h2>Recent Sales</h2>
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('sales')) ?>"><i class="bi bi-arrow-right"></i> View</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Invoice</th><th>Cashier</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td><a href="<?= e(url('sales/view', ['id' => $sale['id']])) ?>"><?= e($sale['invoice_number']) ?></a></td>
                            <td><?= e($sale['cashier']) ?></td>
                            <td><?= e(format_date($sale['sale_date'])) ?></td>
                            <td><?= money($sale['total_amount']) ?></td>
                            <td><span class="badge text-bg-info"><?= e(ucfirst($sale['payment_status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentSales): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No sales recorded yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <div>
        <article class="panel">
            <div class="panel-title">
                <h2>Low Stock Products</h2>
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('inventory/low-stock')) ?>"><i class="bi bi-arrow-right"></i> View</a>
            </div>
            <div class="summary-list">
                <?php foreach ($lowProducts as $product): ?>
                    <div class="summary-row">
                        <div>
                            <strong><?= e($product['product_name']) ?></strong>
                            <span class="d-block text-muted small"><?= e($product['product_code']) ?> - <?= e($product['category_name']) ?></span>
                        </div>
                        <span><?= (int) $product['quantity'] ?> / <?= (int) $product['reorder_level'] ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$lowProducts): ?>
                    <p class="text-muted mb-0">No low-stock products.</p>
                <?php endif; ?>
            </div>
        </article>

        <article class="panel">
            <div class="panel-title"><h2>Top Selling Products</h2></div>
            <div class="summary-list">
                <?php foreach ($topProducts as $product): ?>
                    <div class="summary-row">
                        <span><?= e($product['product_name']) ?></span>
                        <strong><?= number_format((int) $product['quantity_sold']) ?> sold</strong>
                    </div>
                <?php endforeach; ?>
                <?php if (!$topProducts): ?>
                    <p class="text-muted mb-0">Sales data will appear here after transactions.</p>
                <?php endif; ?>
            </div>
        </article>

        <article class="panel">
            <div class="panel-title"><h2>Recent Inventory Activity</h2></div>
            <div class="summary-list">
                <?php foreach ($recentMovements as $movement): ?>
                    <div class="summary-row">
                        <div>
                            <strong><?= e($movement['product_name']) ?></strong>
                            <span class="d-block text-muted small"><?= e(str_replace('_', ' ', $movement['movement_type'])) ?></span>
                        </div>
                        <span><?= (int) $movement['quantity_before'] ?> -> <?= (int) $movement['quantity_after'] ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$recentMovements): ?>
                    <p class="text-muted mb-0">No stock movements yet.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>
