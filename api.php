<?php

if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $fullPath = __DIR__ . $path;
    if (is_file($fullPath)) {
        return false; // Serve static file
    }
}

require_once 'config.php';
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


$router = new Router();

// Public
$router->add('POST', '/api/login', [AuthController::class, 'login']);

// Authenticated user info
$router->add('GET', '/api/whoami', [AuthController::class, 'whoami']);

// Manager-only users CRUD
$router->add('GET', '/api/users', [UserController::class, 'index']);
$router->add('GET', '/api/users/{id}', [UserController::class, 'show']);
$router->add('POST', '/api/users', [UserController::class, 'store']);
$router->add('PUT', '/api/users/{id}', [UserController::class, 'update']);
$router->add('DELETE', '/api/users/{id}', [UserController::class, 'destroy']);

// Employee-only vacations CRUD
$router->add('GET', '/api/vacations', [VacationController::class, 'index']);
$router->add('POST', '/api/vacations', [VacationController::class, 'store']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
