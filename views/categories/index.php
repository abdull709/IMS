<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('categories')) ?>">
        <input type="hidden" name="route" value="categories">
        <input class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search categories">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <a class="btn btn-primary" href="<?= e(url('categories/create')) ?>"><i class="bi bi-plus-circle"></i> Add Category</a>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $index => $category): ?>
                    <tr>
                        <td><?= (($page - 1) * 10) + $index + 1 ?></td>
                        <td><strong><?= e($category['category_name']) ?></strong></td>
                        <td><?= e($category['description']) ?></td>
                        <td><?= number_format((int) $category['product_count']) ?></td>
                        <td><?= badge_status($category['status']) ?></td>
                        <td><?= e(format_date($category['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('categories/edit', ['id' => $category['id']])) ?>" title="Edit category"><i class="bi bi-pencil"></i></a>
                                <form method="post" action="<?= e(url('categories/delete')) ?>" onsubmit="return confirm('Delete this category when safe, or deactivate it when products exist?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete or deactivate"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'categories', ['search' => $search]) ?></div>
</section>
