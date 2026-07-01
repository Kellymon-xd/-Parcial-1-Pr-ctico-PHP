<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use Itech\Controllers\InscripcionController;

$controller = new InscripcionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->guardar();
    exit;
}

$controller->formulario();
