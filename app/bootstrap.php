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

if (!is_dir(STORAGE_PATH . '/logs')) {
    mkdir(STORAGE_PATH . '/logs', 0775, true);
}
