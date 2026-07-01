<?php
use Itech\Utils\Sanitizador;

require __DIR__ . '/../layout/header.php';
?>

<section class="card wide-card">
    <div class="section-title">
        <div>
            <p class="eyebrow">Auditoría de registros</p>
            <h2>Reporte de datos guardados</h2>
        </div>
        <div class="action-row">
            <a href="index.php" class="btn btn-secondary">Nuevo registro</a>
            <a href="exportar_excel.php" class="btn btn-primary">Exportar Excel</a>
        </div>
    </div>

    <p class="report-note">
        Los temas tecnológicos se muestran separados por comas. La columna de integridad verifica con OpenSSL los datos
        principales del registro: identidad, nombre, apellido, edad, sexo, país, nacionalidad, correo y celular.
    </p>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Identidad</th>
                <th>Nombre completo</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>País</th>
                <th>Nacionalidad</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Temas</th>
                <th>Observaciones</th>
                <th>Fecha</th>
                <th>Integridad</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="13" class="empty-state">Todavía no hay registros guardados.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($registros as $registro): ?>
                <tr class="<?= !empty($registro['integridad_valida']) ? 'row-valid' : 'row-invalid' ?>">
                    <td><?= (int) ($registro['id'] ?? 0) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['identidad'] ?? '')) ?></td>
                    <td><?= Sanitizador::html(trim((string) ($registro['nombre'] ?? '') . ' ' . (string) ($registro['apellido'] ?? ''))) ?></td>
                    <td><?= (int) ($registro['edad'] ?? 0) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['sexo'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['pais_residencia'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['nacionalidad'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['correo'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['celular'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['temas'] ?? '')) ?></td>
                    <td><?= Sanitizador::html((string) ($registro['observaciones'] ?? '')) ?></td>
                    <td>
                        <?php
                        $fecha = $registro['fecha_registro'] ?? null;
                        echo $fecha ? Sanitizador::html(date('d/m/Y H:i', strtotime((string) $fecha))) : 'Sin fecha';
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($registro['integridad_valida'])): ?>
                            <span class="status status-ok">Verde: validado</span>
                        <?php else: ?>
                            <span class="status status-bad">Rojo: vulnerado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
