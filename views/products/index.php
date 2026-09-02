<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('products')) ?>">
        <input type="hidden" name="route" value="products">
        <input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="Search product, code, or category">
        <select class="form-select" name="category_id">
            <option value="">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= selected($filters['category_id'], $category['id']) ?>><?= e($category['category_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select" name="stock">
            <option value="">All stock</option>
            <option value="low" <?= selected($filters['stock'], 'low') ?>>Low stock</option>
            <option value="out" <?= selected($filters['stock'], 'out') ?>>Out of stock</option>
        </select>
        <select class="form-select" name="status">
            <option value="">Any status</option>
            <option value="active" <?= selected($filters['status'], 'active') ?>>Active</option>
            <option value="inactive" <?= selected($filters['status'], 'inactive') ?>>Inactive</option>
        </select>
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <?php if (is_admin()): ?>
        <a class="btn btn-primary" href="<?= e(url('products/create')) ?>"><i class="bi bi-plus-circle"></i> Add Product</a>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Quantity</th>
                    <th>Reorder Level</th>
                    <th>Stock Status</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?= (($page - 1) * 10) + $index + 1 ?></td>
                        <td><?= e($product['product_code']) ?></td>
                        <td><strong><?= e($product['product_name']) ?></strong></td>
                        <td><?= e($product['category_name']) ?></td>
                        <td><?= money($product['cost_price']) ?></td>
                        <td><?= money($product['selling_price']) ?></td>
                        <td><?= number_format((int) $product['quantity']) ?> <?= e($product['unit']) ?></td>
                        <td><?= number_format((int) $product['reorder_level']) ?></td>
                        <td><?= badge_stock((int) $product['quantity'], (int) $product['reorder_level']) ?></td>
                        <td><?= badge_status($product['status']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('products/view', ['id' => $product['id']])) ?>" title="View product"><i class="bi bi-eye"></i></a>
                                <?php if (is_admin()): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(url('products/edit', ['id' => $product['id']])) ?>" title="Edit product"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="<?= e(url('products/delete')) ?>" onsubmit="return confirm('Delete this product when safe, or deactivate it when sales history exists?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete or deactivate"><i class="bi bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'products', $filters) ?></div>
</section>
