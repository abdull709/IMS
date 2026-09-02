<section class="page-actions">
    <a class="btn btn-outline-secondary" href="<?= e(url('sales')) ?>"><i class="bi bi-arrow-left"></i> Sales History</a>
</section>

<form method="post" action="<?= e(url('sales/store')) ?>" data-sale-form>
    <?= csrf_field() ?>
    <input type="hidden" name="items_json" value="[]">

    <section class="split-grid">
        <article class="panel">
            <div class="panel-title"><h2>Select Products</h2></div>
            <div class="row g-3 align-items-end">
                <div class="col-12">
                    <label class="form-label" for="product_search">Search Product</label>
                    <input class="form-control" id="product_search" data-product-search data-search-url="<?= e(url('api/products/search')) ?>" placeholder="Type product name, code, or category">
                </div>
                <div class="col-lg-7">
                    <label class="form-label" for="product_select">Product</label>
                    <select class="form-select" id="product_select" data-product-select>
                        <option value="">Search and select product</option>
                        <?php foreach ($products as $product): ?>
                            <option
                                value="<?= (int) $product['id'] ?>"
                                data-code="<?= e($product['product_code']) ?>"
                                data-name="<?= e($product['product_name']) ?>"
                                data-price="<?= e($product['selling_price']) ?>"
                                data-quantity="<?= (int) $product['quantity'] ?>"
                                data-unit="<?= e($product['unit']) ?>"
                            >
                                <?= e($product['product_code'] . ' - ' . $product['product_name'] . ' (' . $product['quantity'] . ' available)') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label" for="quantity">Quantity</label>
                    <input class="form-control" id="quantity" data-quantity type="number" min="1" value="1">
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary w-100" type="button" data-add-item><i class="bi bi-cart-plus"></i> Add Item</button>
                </div>
            </div>
            <p class="text-danger small mb-0 mt-2" data-sale-message></p>

            <div class="table-responsive mt-4">
                <table class="table align-middle">
                    <thead><tr><th>Product</th><th>Available</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody data-sale-rows></tbody>
                    <tbody data-sale-empty>
                        <tr><td colspan="6" class="text-center text-muted py-4">No products added to this sale.</td></tr>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <div class="panel-title"><h2>Payment Summary</h2></div>
            <div class="summary-list">
                <div class="summary-row"><span>Subtotal</span><strong><?= e(setting('currency', 'NGN ')) ?><span data-subtotal>0.00</span></strong></div>
                <div>
                    <label class="form-label" for="discount">Discount</label>
                    <input class="form-control" id="discount" name="discount" type="number" min="0" step="0.01" value="0">
                </div>
                <div class="summary-row"><span>Total</span><strong><?= e(setting('currency', 'NGN ')) ?><span data-total>0.00</span></strong></div>
                <div>
                    <label class="form-label" for="amount_paid">Amount Paid</label>
                    <input class="form-control" id="amount_paid" name="amount_paid" type="number" min="0" step="0.01" value="0">
                </div>
                <div class="summary-row"><span>Balance</span><strong><?= e(setting('currency', 'NGN ')) ?><span data-balance>0.00</span></strong></div>
                <div>
                    <label class="form-label" for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
            <button class="btn btn-primary w-100 mt-4" type="submit"><i class="bi bi-save"></i> Save Sale</button>
        </article>
    </section>
</form>
