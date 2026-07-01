<?php

declare(strict_types=1);

namespace Itech\Services;

final class ExcelExportService
{
    public function descargar(array $registros): void
    {
        $autoload = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        if (is_file($autoload)) {
            require_once $autoload;
        }

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $this->descargarConPhpSpreadsheet($registros);
            return;
        }

        $this->descargarHtmlCompatibleExcel($registros);
    }

    private function descargarConPhpSpreadsheet(array $registros): void
    {
        $documento = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $documento->getProperties()
            ->setCreator('ITECH')
            ->setLastModifiedBy('ITECH')
            ->setTitle('Reporte de inscriptores')
            ->setDescription('Reporte exportado desde MariaDB usando PhpSpreadsheet');

        $hoja = $documento->getActiveSheet();
        $hoja->setTitle('Inscriptores');

        $encabezados = $this->encabezados();
        $hoja->fromArray($encabezados, null, 'A1');

        $fila = 2;
        foreach ($registros as $registro) {
            $hoja->fromArray($this->mapearRegistro($registro), null, 'A' . $fila);
            $fila++;
        }

        foreach (range('A', 'M') as $columna) {
            $hoja->getColumnDimension($columna)->setAutoSize(true);
        }

        $hoja->getStyle('A1:M1')->getFont()->setBold(true);
        $hoja->freezePane('A2');

        $nombreArchivo = 'inscripciones_itech_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($documento);
        $writer->save('php://output');
        exit;
    }

    private function descargarHtmlCompatibleExcel(array $registros): void
    {
        $nombreArchivo = 'inscripciones_itech_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        echo '<table border="1">';
        echo '<tr>';

        foreach ($this->encabezados() as $encabezado) {
            echo '<th>' . htmlspecialchars($encabezado, ENT_QUOTES, 'UTF-8') . '</th>';
        }

        echo '</tr>';

        foreach ($registros as $registro) {
            echo '<tr>';
            foreach ($this->mapearRegistro($registro) as $valor) {
                echo '<td>' . htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }

    private function encabezados(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellido',
            'Edad',
            'Sexo',
            'País de residencia',
            'Nacionalidad',
            'Correo',
            'Celular',
            'Temas tecnológicos',
            'Observaciones',
            'Fecha de registro',
            'Integridad',
        ];
    }

    private function mapearRegistro(array $registro): array
    {
        return [
            $registro['id'] ?? '',
            $registro['nombre'] ?? '',
            $registro['apellido'] ?? '',
            $registro['edad'] ?? '',
            $registro['sexo'] ?? '',
            $registro['pais_residencia'] ?? '',
            $registro['nacionalidad'] ?? '',
            $registro['correo'] ?? '',
            $registro['celular'] ?? '',
            $registro['temas'] ?? '',
            $registro['observaciones'] ?? '',
            $registro['fecha_registro'] ?? '',
            $registro['integridad_texto'] ?? '',
        ];
    }
}
