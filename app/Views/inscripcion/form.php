<?php
use Itech\Utils\Sanitizador;

require __DIR__ . '/../layout/header.php';

function old_value(array $old, string $key, string $default = ''): string
{
    return isset($old[$key]) ? (string) $old[$key] : $default;
}

$oldAreas = $old['areas'] ?? [];
$oldAreas = is_array($oldAreas) ? array_map('intval', $oldAreas) : [];
?>

<section class="card">
    <div class="section-title">
        <div>
            <p class="eyebrow">Registro de participantes</p>
            <h2>Datos del inscriptor</h2>
        </div>
        <span class="date-pill">Fecha: <?= date('d/m/Y H:i') ?></span>
    </div>

    <?php if (!empty($ok)): ?>
        <div class="alert alert-success">El registro fue guardado y firmado correctamente.</div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <strong>Revisa lo siguiente:</strong>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= Sanitizador::html($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= Sanitizador::html(csrf_token()) ?>">

        <div class="field">
            <label for="identidad">Identidad <span>*</span></label>
            <input type="text" id="identidad" name="identidad" maxlength="20" required
                   value="<?= Sanitizador::html(old_value($old, 'identidad')) ?>"
                   placeholder="Ejemplo: 8-123-456">
        </div>

        <div class="field">
            <label for="nombre">Nombre <span>*</span></label>
            <input type="text" id="nombre" name="nombre" maxlength="100" required
                   value="<?= Sanitizador::html(old_value($old, 'nombre')) ?>"
                   placeholder="Ejemplo: Kelly">
        </div>

        <div class="field">
            <label for="apellido">Apellido <span>*</span></label>
            <input type="text" id="apellido" name="apellido" maxlength="100" required
                   value="<?= Sanitizador::html(old_value($old, 'apellido')) ?>"
                   placeholder="Ejemplo: Gómez">
        </div>

        <div class="field">
            <label for="edad">Edad <span>*</span></label>
            <input type="number" id="edad" name="edad" min="1" max="120" required
                   value="<?= Sanitizador::html(old_value($old, 'edad')) ?>">
        </div>

        <div class="field">
            <label for="sexo">Sexo <span>*</span></label>
            <select id="sexo" name="sexo" required>
                <option value="">Seleccione...</option>
                <?php foreach (['Masculino', 'Femenino', 'Otro'] as $sexo): ?>
                    <option value="<?= Sanitizador::html($sexo) ?>" <?= old_value($old, 'sexo') === $sexo ? 'selected' : '' ?>>
                        <?= Sanitizador::html($sexo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="pais_residencia_id">País de residencia <span>*</span></label>
            <select id="pais_residencia_id" name="pais_residencia_id" required>
                <option value="">Seleccione...</option>
                <?php foreach ($paises as $pais): ?>
                    <option value="<?= (int) $pais['id'] ?>" <?= (int) old_value($old, 'pais_residencia_id', '0') === (int) $pais['id'] ? 'selected' : '' ?>>
                        <?= Sanitizador::html($pais['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="nacionalidad_id">Nacionalidad <span>*</span></label>
            <select id="nacionalidad_id" name="nacionalidad_id" required>
                <option value="">Seleccione...</option>
                <?php foreach ($paises as $pais): ?>
                    <option value="<?= (int) $pais['id'] ?>" <?= (int) old_value($old, 'nacionalidad_id', '0') === (int) $pais['id'] ? 'selected' : '' ?>>
                        <?= Sanitizador::html($pais['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="correo">Correo <span>*</span></label>
            <input type="email" id="correo" name="correo" maxlength="150" required
                   value="<?= Sanitizador::html(old_value($old, 'correo')) ?>"
                   placeholder="correo@ejemplo.com">
        </div>

        <div class="field">
            <label for="celular">Celular <span>*</span></label>
            <input type="tel" id="celular" name="celular" maxlength="20" required
                   value="<?= Sanitizador::html(old_value($old, 'celular')) ?>"
                   placeholder="6123-4567">
        </div>

        <fieldset class="field field-full checkbox-group">
            <legend>Tema tecnológico que le gustaría aprender <span>*</span></legend>
            <div class="checkbox-grid">
                <?php foreach ($areas as $area): ?>
                    <label class="checkbox-card">
                        <input type="checkbox" name="areas[]" value="<?= (int) $area['id'] ?>"
                            <?= in_array((int) $area['id'], $oldAreas, true) ? 'checked' : '' ?>>
                        <span><?= Sanitizador::html($area['nombre']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <div class="field field-full">
            <label for="observaciones">Observaciones o consulta sobre el evento</label>
            <textarea id="observaciones" name="observaciones" maxlength="500" rows="4"
                      placeholder="Escriba su consulta u observación."><?= Sanitizador::html(old_value($old, 'observaciones')) ?></textarea>
            <small>Máximo 500 caracteres.</small>
        </div>

        <div class="form-actions field-full">
            <button type="submit" class="btn btn-primary">Guardar inscripción</button>
            <a href="reporte.php" class="btn btn-secondary">Ver reporte</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
