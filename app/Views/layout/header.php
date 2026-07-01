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
        <div class="brand-logo">IT</div>
        <div>
            <p class="eyebrow">Universidad Tecnológica de Panamá</p>
            <h1><?= Sanitizador::html($tituloPagina ?? 'Formulario ITECH') ?></h1>
            <p class="subtitle">Departamento de Ingeniería de Software</p>
        </div>
    </div>
    <nav class="nav-actions">
        <a href="index.php">Formulario</a>
        <a href="reporte.php">Reporte</a>
    </nav>
</header>
<main class="container">
