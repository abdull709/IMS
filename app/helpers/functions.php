<?php

declare(strict_types=1);

function config(?string $key = null, mixed $default = null): mixed
{
    $config = $GLOBALS['config'] ?? [];
    if ($key === null) {
        return $config;
    }

    foreach (explode('.', $key) as $segment) {
        if (!is_array($config) || !array_key_exists($segment, $config)) {
            return $default;
        }
        $config = $config[$segment];
    }

    return $config;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_base_url(): string
{
    $configured = trim((string) config('app.base_url', ''));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    if ($scriptDir === '/' || $scriptDir === '.') {
        $scriptDir = '';
    }

    if (str_ends_with($scriptDir, '/public')) {
        $scriptDir = substr($scriptDir, 0, -7);
    }

    return rtrim($scheme . '://' . $host . $scriptDir, '/');
}

function url(string $path = '', array $params = []): string
{
    $path = trim($path, '/');
    $base = app_base_url();

    if (config('app.clean_urls', false)) {
        $target = $base . ($path === '' ? '/' : '/' . $path);
        if ($params !== []) {
            $target .= '?' . http_build_query($params);
        }
        return $target;
    }

    if ($path !== '') {
        $params = ['route' => $path] + $params;
    }

    $query = $params === [] ? '' : '?' . http_build_query($params);
    return $base . '/' . $query;
}

function asset(string $path): string
{
    $prefix = trim((string) config('app.asset_prefix', 'public'), '/');
    $path = ltrim($path, '/');
    return app_base_url() . '/' . ($prefix === '' ? '' : $prefix . '/') . $path;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function previous_url(): string
{
    return $_SERVER['HTTP_REFERER'] ?? url('dashboard');
}

function input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? null) === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(url('login'));
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        $code = '403';
        $title = 'Access Denied';
        $message = 'You do not have permission to access this area.';
        ob_start();
        require VIEW_PATH . '/errors/generic.php';
        $content = ob_get_clean();
        require VIEW_PATH . '/layouts/main.php';
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array
{
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flash;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function set_old(array $values): void
{
    $_SESSION['_old'] = $values;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function money(float|int|string $amount): string
{
    $currency = setting('currency', 'NGN ');
    return $currency . number_format((float) $amount, 2);
}

function format_date(?string $value, string $format = 'd M Y, h:i A'): string
{
    if (!$value) {
        return '-';
    }
    return date($format, strtotime($value));
}

function badge_stock(int $quantity, int $reorderLevel): string
{
    if ($quantity <= 0) {
        return '<span class="badge text-bg-danger">Out of Stock</span>';
    }
    if ($quantity <= $reorderLevel) {
        return '<span class="badge text-bg-warning">Low Stock</span>';
    }
    return '<span class="badge text-bg-success">In Stock</span>';
}

function badge_status(string $status): string
{
    $class = $status === 'active' ? 'text-bg-success' : 'text-bg-secondary';
    return '<span class="badge ' . $class . '">' . e(ucfirst($status)) . '</span>';
}

function selected(mixed $left, mixed $right): string
{
    return (string) $left === (string) $right ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function active_nav(string $prefix): string
{
    $route = trim((string) ($_GET['route'] ?? ''), '/');
    return $route === $prefix || str_starts_with($route, trim($prefix, '/') . '/') ? 'active' : '';
}

function paginate_links(int $page, int $pages, string $route, array $params = []): string
{
    if ($pages <= 1) {
        return '';
    }

    $html = '<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">';
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e(url($route, $params + ['page' => $i])) . '">' . $i . '</a></li>';
    }
    $html .= '</ul></nav>';

    return $html;
}

function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        try {
            $model = new \App\Models\Setting();
            $settings = $model->allKeyed();
        } catch (\Throwable) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}
