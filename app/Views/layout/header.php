<?php
use Itech\Utils\Sanitizador;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Sanitizador::html($tituloPagina ?? 'ITECH') ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header class="site-header">
    <div class="brand">
        <div class="brand-logo">
            <span>IT</span>
        </div>
        <div class="brand-copy">
            <p class="eyebrow">Registro académico digital</p>
            <h1><?= Sanitizador::html($tituloPagina ?? 'Formulario ITECH') ?></h1>
            <p class="subtitle">Evento ITECH · Inscripción, auditoría y exportación</p>
        </div>
    </div>

    <nav class="nav-actions" aria-label="Navegación principal">
        <a href="index.php">Formulario</a>
        <a href="reporte.php">Reporte</a>
    </nav>
</header>
<main class="container">