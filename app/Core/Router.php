<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$this->normalize($path)] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$this->normalize($path)] = $action;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->currentPath();
        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            $this->renderError('404', 'Page Not Found', 'The page you requested does not exist.');
            return;
        }

        [$controllerName, $methodName] = explode('@', $action);
        $class = 'App\\Controllers\\' . $controllerName;

        try {
            $controller = new $class();
            $controller->{$methodName}();
        } catch (\Throwable $exception) {
            app_log_exception($exception);

            if (config('app.debug', false)) {
                http_response_code(500);
                echo '<pre>' . htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8') . '</pre>';
                return;
            }

            http_response_code(500);
            $message = 'The application encountered an unexpected error.';
            if (is_admin()) {
                $message = 'Database/application error: ' . $exception->getMessage();
            }
            $this->renderError('500', 'Server Error', $message);
        }
    }

    private function currentPath(): string
    {
        if (isset($_GET['route'])) {
            return $this->normalize((string) $_GET['route']);
        }

        $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim($scriptDir, '/');

        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($uriPath, $scriptDir)) {
            $uriPath = substr($uriPath, strlen($scriptDir));
        }

        if (str_starts_with($uriPath, '/public')) {
            $uriPath = substr($uriPath, strlen('/public'));
        }

        return $this->normalize($uriPath);
    }

    private function normalize(string $path): string
    {
        $path = trim($path, '/');
        return $path === 'index.php' ? '' : $path;
    }

    private function renderError(string $code, string $title, string $message): void
    {
        $viewFile = VIEW_PATH . '/errors/generic.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require VIEW_PATH . '/layouts/main.php';
    }
}
