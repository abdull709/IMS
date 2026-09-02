<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-mark"><i class="bi bi-box-seam"></i></div>
        <div>
            <strong>IMS</strong>
            <span>Inventory Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Main navigation">
        <a class="<?= active_nav('dashboard') ?>" href="<?= e(url('dashboard')) ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

        <div class="nav-group">Inventory</div>
        <a class="<?= active_nav('products') ?>" href="<?= e(url('products')) ?>"><i class="bi bi-box"></i><span>Products</span></a>
        <?php if (is_admin()): ?>
            <a class="<?= active_nav('categories') ?>" href="<?= e(url('categories')) ?>"><i class="bi bi-tags"></i><span>Categories</span></a>
        <?php endif; ?>
        <a class="<?= active_nav('inventory') ?>" href="<?= e(url('inventory')) ?>"><i class="bi bi-stack"></i><span>Stock Management</span></a>
        <a class="<?= active_nav('inventory/low-stock') ?>" href="<?= e(url('inventory/low-stock')) ?>"><i class="bi bi-exclamation-triangle"></i><span>Low Stock</span></a>
        <a class="<?= active_nav('inventory/movements') ?>" href="<?= e(url('inventory/movements')) ?>"><i class="bi bi-clock-history"></i><span>Stock Movements</span></a>

        <div class="nav-group">Sales</div>
        <a class="<?= active_nav('sales/create') ?>" href="<?= e(url('sales/create')) ?>"><i class="bi bi-cart-plus"></i><span>New Sale</span></a>
        <a class="<?= active_nav('sales') ?>" href="<?= e(url('sales')) ?>"><i class="bi bi-receipt"></i><span>Sales History</span></a>

        <?php if (is_admin()): ?>
            <div class="nav-group">Reports</div>
            <a class="<?= active_nav('reports/inventory') ?>" href="<?= e(url('reports/inventory')) ?>"><i class="bi bi-clipboard-data"></i><span>Inventory Report</span></a>
            <a class="<?= active_nav('reports/sales') ?>" href="<?= e(url('reports/sales')) ?>"><i class="bi bi-graph-up-arrow"></i><span>Sales Report</span></a>
            <a class="<?= active_nav('reports/low-stock') ?>" href="<?= e(url('reports/low-stock')) ?>"><i class="bi bi-bell"></i><span>Low Stock Report</span></a>
            <a class="<?= active_nav('reports/stock-movements') ?>" href="<?= e(url('reports/stock-movements')) ?>"><i class="bi bi-arrow-left-right"></i><span>Stock Report</span></a>
            <a class="<?= active_nav('reports/product-sales') ?>" href="<?= e(url('reports/product-sales')) ?>"><i class="bi bi-bar-chart"></i><span>Product Sales</span></a>

            <div class="nav-group">Administration</div>
            <a class="<?= active_nav('users') ?>" href="<?= e(url('users')) ?>"><i class="bi bi-people"></i><span>Users</span></a>
            <a class="<?= active_nav('settings') ?>" href="<?= e(url('settings')) ?>"><i class="bi bi-gear"></i><span>Settings</span></a>
        <?php endif; ?>

        <div class="nav-group">Account</div>
        <a class="<?= active_nav('profile') ?>" href="<?= e(url('profile')) ?>"><i class="bi bi-person-circle"></i><span>Profile</span></a>
        <a href="<?= e(url('logout')) ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
    </nav>
</aside>
