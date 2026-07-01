<?php

declare(strict_types=1);

date_default_timezone_set('America/Panama');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefix = 'Itech\\';
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

function view(string $path, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/Views/' . $path . '.php';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
