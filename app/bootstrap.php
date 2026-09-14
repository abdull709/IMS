<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('VIEW_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'views');
define('STORAGE_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'storage');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/helpers/functions.php';

$configFile = ROOT_PATH . '/config.php';
if (!is_file($configFile)) {
    $configFile = ROOT_PATH . '/config.example.php';
}

$GLOBALS['config'] = require $configFile;

date_default_timezone_set(config('app.timezone', 'UTC'));

$logDir = STORAGE_PATH . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

ini_set('log_errors', '1');
ini_set('display_errors', config('app.debug', false) ? '1' : '0');
error_reporting(E_ALL);

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }

    app_log("PHP error [{$severity}] {$message} in {$file}:{$line}");
    return false;
});

set_exception_handler(static function (\Throwable $exception): void {
    app_log_exception($exception);

    if (!headers_sent()) {
        http_response_code(500);
    }

    if (config('app.debug', false)) {
        echo '<pre>' . htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }

    echo 'The application encountered an unexpected error.';
});

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error === null) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    app_log("Fatal PHP error [{$error['type']}] {$error['message']} in {$error['file']}:{$error['line']}");
});
