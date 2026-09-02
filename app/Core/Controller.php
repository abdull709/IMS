<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
    }

    protected function requireLogin(): void
    {
        require_login();
    }

    protected function requireAdmin(): void
    {
        require_admin();
    }

    protected function validateCsrf(): void
    {
        if (!verify_csrf(input('_token'))) {
            set_flash('danger', 'Your session token expired. Please try again.');
            redirect(previous_url());
        }
    }
}
