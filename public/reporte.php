<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use Itech\Controllers\ReporteController;

$controller = new ReporteController();
$controller->index();
