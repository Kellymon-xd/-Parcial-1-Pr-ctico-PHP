<?php

declare(strict_types=1);

namespace Itech\Controllers;

use Itech\Models\AreaInteres;
use Itech\Models\Inscriptor;
use Itech\Models\Pais;
use Itech\Utils\Sanitizador;
use Itech\Utils\Validador;
use Throwable;

final class InscripcionController
{
    private Pais $paisModel;
    private AreaInteres $areaModel;
    private Inscriptor $inscriptorModel;

    public function __construct()
    {
        $this->paisModel = new Pais();
        $this->areaModel = new AreaInteres();
        $this->inscriptorModel = new Inscriptor();
    }

    public function formulario(array $errores = [], array $old = []): void
    {
        view('inscripcion/form', [
            'tituloPagina' => 'Formulario de inscripción ITECH',
            'paises' => $this->paisModel->obtenerTodos(),
            'areas' => $this->areaModel->obtenerTodas(),
            'errores' => $errores,
            'old' => $old,
            'ok' => isset($_GET['ok']),
        ]);
    }

    public function guardar(): void
    {
        if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
            $this->formulario(['La sesión expiró. Actualiza la página e intenta nuevamente.'], $_POST);
            return;
        }

        $datos = [
            'identidad' => Sanitizador::texto($_POST['identidad'] ?? ''),
            'nombre' => Sanitizador::titulo($_POST['nombre'] ?? ''),
            'apellido' => Sanitizador::titulo($_POST['apellido'] ?? ''),
            'edad' => Sanitizador::texto($_POST['edad'] ?? ''),
            'sexo' => Sanitizador::texto($_POST['sexo'] ?? ''),
            'pais_residencia_id' => (int) ($_POST['pais_residencia_id'] ?? 0),
            'nacionalidad_id' => (int) ($_POST['nacionalidad_id'] ?? 0),
            'correo' => Sanitizador::correo($_POST['correo'] ?? ''),
            'celular' => Sanitizador::telefono($_POST['celular'] ?? ''),
            'observaciones' => Sanitizador::texto($_POST['observaciones'] ?? ''),
        ];

        $areas = $_POST['areas'] ?? [];
        $areas = is_array($areas) ? $areas : [];

        $errores = $this->validar($datos, $areas);

        if ($errores !== []) {
            $old = $datos;
            $old['areas'] = $areas;

            $this->formulario($errores, $old);
            return;
        }

        $datos['edad'] = (int) $datos['edad'];
        $datos['observaciones'] = $datos['observaciones'] !== '' ? $datos['observaciones'] : null;

        try {
            $this->inscriptorModel->guardar($datos, $areas);
            redirect('index.php?ok=1');
        } catch (Throwable $exception) {
            $mensaje = 'No se pudo guardar el registro.';

            if (strpos($exception->getMessage(), 'Duplicate') !== false || strpos($exception->getMessage(), '23000') !== false) {
                $mensaje = 'La identidad o el correo ya están registrados.';
            }

            $old = $datos;
            $old['areas'] = $areas;

            $this->formulario([$mensaje], $old);
        }
    }

    private function validar(array $datos, array $areas): array
    {
        $errores = [];

        if (!Validador::identidad($datos['identidad'])) {
            $errores[] = 'La identidad debe tener entre 4 y 20 caracteres. Solo puede usar letras, números y guiones.';
        }

        if (!Validador::nombre($datos['nombre'])) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres y solo debe contener letras.';
        }

        if (!Validador::nombre($datos['apellido'])) {
            $errores[] = 'El apellido debe tener al menos 2 caracteres y solo debe contener letras.';
        }

        if (!Validador::edad($datos['edad'])) {
            $errores[] = 'La edad debe ser un número entero entre 1 y 120.';
        }

        if (!Validador::sexo($datos['sexo'])) {
            $errores[] = 'Selecciona un sexo válido.';
        }

        if (!Validador::id($datos['pais_residencia_id']) || !$this->paisModel->existe((int) $datos['pais_residencia_id'])) {
            $errores[] = 'Selecciona un país de residencia válido.';
        }

        if (!Validador::id($datos['nacionalidad_id']) || !$this->paisModel->existe((int) $datos['nacionalidad_id'])) {
            $errores[] = 'Selecciona una nacionalidad válida.';
        }

        if (!Validador::correo($datos['correo'])) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }

        if (!Validador::celular($datos['celular'])) {
            $errores[] = 'Ingresa un celular válido. Ejemplo: 6123-4567.';
        }

        if (!Validador::areas($areas) || !$this->areaModel->existenTodas($areas)) {
            $errores[] = 'Selecciona al menos un tema tecnológico válido.';
        }

        if (!Validador::longitudMaxima((string) $datos['observaciones'], 500)) {
            $errores[] = 'Las observaciones no deben superar los 500 caracteres.';
        }

        return $errores;
    }
}
