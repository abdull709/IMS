<section class="page-actions no-print">
    <a class="btn btn-outline-secondary" href="<?= e(url('sales')) ?>"><i class="bi bi-arrow-left"></i> Back</a>
    <button class="btn btn-primary" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
</section>

<section class="receipt">
    <div class="receipt-header">
        <h2><?= e(setting('business_name', 'Local Business')) ?></h2>
        <p class="mb-1"><?= e(setting('business_address', 'Business address')) ?></p>
        <p class="mb-0"><?= e(setting('business_phone', '')) ?> <?= setting('business_email', '') ? ' | ' . e(setting('business_email')) : '' ?></p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <span class="text-muted d-block">Invoice Number</span>
            <strong><?= e($sale['invoice_number']) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Sale Date</span>
            <strong><?= e(format_date($sale['sale_date'])) ?></strong>
        </div>
        <div class="col-md-4">
            <span class="text-muted d-block">Cashier</span>
            <strong><?= e($sale['cashier']) ?></strong>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><strong><?= e($item['product_name']) ?></strong><span class="d-block text-muted small"><?= e($item['product_code']) ?></span></td>
                        <td><?= number_format((int) $item['quantity']) ?> <?= e($item['unit']) ?></td>
                        <td><?= money($item['unit_price']) ?></td>
                        <td><?= money($item['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="summary-list">
                <div class="summary-row"><span>Subtotal</span><strong><?= money($sale['subtotal']) ?></strong></div>
                <div class="summary-row"><span>Discount</span><strong><?= money($sale['discount']) ?></strong></div>
                <div class="summary-row"><span>Grand Total</span><strong><?= money($sale['total_amount']) ?></strong></div>
                <div class="summary-row"><span>Amount Paid</span><strong><?= money($sale['amount_paid']) ?></strong></div>
                <div class="summary-row"><span>Balance</span><strong><?= money($sale['balance']) ?></strong></div>
            </div>
        </div>
    </div>

    <?php if ($sale['notes']): ?>
        <p class="mt-4"><strong>Notes:</strong> <?= e($sale['notes']) ?></p>
    <?php endif; ?>
    <p class="text-center text-muted mt-4 mb-0"><?= e(setting('receipt_footer', 'Thank you for your patronage.')) ?></p>
</section>
