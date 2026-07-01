<?php

declare(strict_types=1);

namespace Itech\Controllers;

use Itech\Models\Inscriptor;
use Itech\Services\ExcelExportService;
use Itech\Services\FirmaDigitalService;

final class ReporteController
{
    private Inscriptor $inscriptorModel;
    private FirmaDigitalService $firmaService;

    public function __construct()
    {
        $this->inscriptorModel = new Inscriptor();
        $this->firmaService = new FirmaDigitalService();
    }

    public function index(): void
    {
        view('reporte/index', [
            'tituloPagina' => 'Reporte de inscripciones',
            'registros' => $this->prepararRegistros(),
        ]);
    }

    public function exportarExcel(): void
    {
        $excel = new ExcelExportService();
        $excel->descargar($this->prepararRegistros());
    }

    private function prepararRegistros(): array
    {
        $registros = $this->inscriptorModel->obtenerReporte();

        foreach ($registros as &$registro) {
            $firmaDigital = (string) ($registro['firma_digital'] ?? '');

            $valido = $firmaDigital !== ''
                && $this->firmaService->verificar($registro, $firmaDigital);

            $registro['integridad_valida'] = $valido;
            $registro['integridad_texto'] = $valido
                ? 'Validado con integridad completa'
                : 'Alerta: registro vulnerado o corrupto';
        }

        unset($registro);

        return $registros;
    }
}
