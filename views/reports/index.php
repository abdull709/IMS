<section class="row g-3">
    <?php
        $cards = [
            ['Inventory Report', 'Current products, stock status, and stock value.', 'reports/inventory', 'bi-clipboard-data'],
            ['Sales Report', 'Transactions, totals, and date range summaries.', 'reports/sales', 'bi-graph-up-arrow'],
            ['Low Stock Report', 'Products at or below their reorder level.', 'reports/low-stock', 'bi-bell'],
            ['Stock Movement Report', 'Detailed inventory movement history.', 'reports/stock-movements', 'bi-arrow-left-right'],
            ['Product Sales Report', 'Quantity sold and sales amount by product.', 'reports/product-sales', 'bi-bar-chart'],
        ];
    ?>
    <?php foreach ($cards as [$name, $description, $route, $icon]): ?>
        <div class="col-md-6 col-xl-4">
            <div class="metric-card h-100">
                <i class="bi <?= e($icon) ?>"></i>
                <strong class="h5 mb-1"><?= e($name) ?></strong>
                <span><?= e($description) ?></span>
                <a class="btn btn-sm btn-primary mt-3" href="<?= e(url($route)) ?>"><i class="bi bi-arrow-right"></i> Open</a>
            </div>
        </div>
    <?php endforeach; ?>
</section>
