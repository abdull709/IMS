<?php

declare(strict_types=1);

// Load controllers without opening a database connection. PHP checks inherited
// method signatures when classes load, which a per-file syntax check can miss.
$root = dirname(__DIR__);
spl_autoload_register(static function (string $class) use ($root): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $file = $root . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$controllers = glob($root . '/app/Controllers/*Controller.php');
if ($controllers === false || $controllers === []) {
    throw new RuntimeException('No controllers found.');
}

foreach ($controllers as $file) {
    $class = 'App\\Controllers\\' . basename($file, '.php');
    if (!class_exists($class)) {
        throw new RuntimeException("Controller could not be loaded: {$class}");
    }
}

$source = file_get_contents($root . '/public/index.php');
if ($source === false) {
    throw new RuntimeException('The route definitions could not be read.');
}

preg_match_all('/\$router->(?:get|post)\(\x27[^\x27]*\x27,\s*\x27([^\x27]+)\x27\)/', $source, $matches);
if ($matches[1] === []) {
    throw new RuntimeException('No route actions found.');
}

foreach ($matches[1] as $action) {
    [$controller, $method] = explode('@', $action, 2);
    $handler = new ReflectionMethod('App\\Controllers\\' . $controller, $method);
    if (!$handler->isPublic() || $handler->isStatic() || $handler->getNumberOfRequiredParameters() !== 0) {
        throw new RuntimeException("Route action cannot be called by the router: {$action}");
    }
}

echo 'PASS: ' . count($controllers) . ' controllers load and ' . count($matches[1]) . ' route actions are callable.' . PHP_EOL;
