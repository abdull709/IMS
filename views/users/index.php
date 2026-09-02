<section class="page-actions">
    <form class="filter-bar" method="get" action="<?= e(url('users')) ?>">
        <input type="hidden" name="route" value="users">
        <input class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search users">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <a class="btn btn-primary" href="<?= e(url('users/create')) ?>"><i class="bi bi-person-plus"></i> Add User</a>
</section>

<section class="panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>#</th><th>Full Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($users as $index => $userRow): ?>
                    <tr>
                        <td><?= (($page - 1) * 10) + $index + 1 ?></td>
                        <td><strong><?= e($userRow['full_name']) ?></strong></td>
                        <td><?= e($userRow['username']) ?></td>
                        <td><?= e($userRow['email']) ?></td>
                        <td><span class="badge text-bg-info"><?= e(ucfirst($userRow['role'])) ?></span></td>
                        <td><?= badge_status($userRow['status']) ?></td>
                        <td><?= e(format_date($userRow['last_login'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('users/edit', ['id' => $userRow['id']])) ?>" title="Edit user"><i class="bi bi-pencil"></i></a>
                                <?php if ((int) $userRow['id'] !== (int) current_user()['id']): ?>
                                    <form method="post" action="<?= e(url('users/delete')) ?>" onsubmit="return confirm('Deactivate this user account?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $userRow['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Deactivate user"><i class="bi bi-person-dash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3"><?= paginate_links($page, $pages, 'users', ['search' => $search]) ?></div>
</section>
