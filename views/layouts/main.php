<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? config('app.name')) ?> - <?= e(config('app.name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body>
    <?php $user = current_user(); ?>
    <div class="app-shell">
        <?php if ($user): ?>
            <?php require VIEW_PATH . '/partials/sidebar.php'; ?>
        <?php endif; ?>
        <div class="app-main">
            <?php if ($user): ?>
                <?php require VIEW_PATH . '/partials/topbar.php'; ?>
            <?php endif; ?>
            <main class="content-wrap">
                <?php require VIEW_PATH . '/partials/flash.php'; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('assets/js/app.js')) ?>"></script>
</body>
</html>
