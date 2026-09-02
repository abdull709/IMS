<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('sales')) ?>">
        <input type="hidden" name="route" value="sales">
        <input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="Search invoice">
        <input class="form-control" name="start_date" type="date" value="<?= e($filters['start_date']) ?>">
        <input class="form-control" name="end_date" type="date" value="<?= e($filters['end_date']) ?>">
        <?php if (is_admin()): ?>
            <select class="form-select" name="user_id">
                <option value="">All users</option>
                <?php foreach ($users as $userRow): ?>
                    <option value="<?= (int) $userRow['id'] ?>" <?= selected($filters['user_id'], $userRow['id']) ?>><?= e($userRow['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <a class="btn btn-primary" href="<?= e(url('sales/create')) ?>"><i class="bi bi-cart-plus"></i> New Sale</a>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Cashier/User</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><strong><?= e($sale['invoice_number']) ?></strong></td>
                        <td><?= e(format_date($sale['sale_date'])) ?></td>
                        <td><?= e($sale['cashier']) ?></td>
                        <td><?= number_format((int) $sale['item_count']) ?></td>
                        <td><?= money($sale['total_amount']) ?></td>
                        <td><?= money($sale['amount_paid']) ?></td>
                        <td><?= money($sale['balance']) ?></td>
                        <td><span class="badge text-bg-info"><?= e(ucfirst($sale['payment_status'])) ?></span></td>
                        <td>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('sales/view', ['id' => $sale['id']])) ?>" title="View receipt"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$sales): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No sales found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'sales', $filters) ?></div>
</section>
