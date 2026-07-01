<?php

declare(strict_types=1);

namespace Itech\Services;

use RuntimeException;

final class FirmaDigitalService
{
    private const OPENSSL_CONFIG_XAMPP = 'C:/xampp/apache/conf/openssl.cnf';

    private string $rutaPrivada;
    private string $rutaPublica;

    public function __construct()
    {
        $base = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'keys';

        $this->rutaPrivada = $base . DIRECTORY_SEPARATOR . 'private.pem';
        $this->rutaPublica = $base . DIRECTORY_SEPARATOR . 'public.pem';

        $this->asegurarLlaves();
    }

    public function firmar(array $datos): string
    {
        $contenido = $this->crearContenidoCanonico($datos);
        $llavePrivadaContenido = file_get_contents($this->rutaPrivada);

        if ($llavePrivadaContenido === false) {
            throw new RuntimeException('No se pudo leer el archivo de la llave privada.');
        }

        $llavePrivada = openssl_pkey_get_private($llavePrivadaContenido);

        if ($llavePrivada === false) {
            throw new RuntimeException('No se pudo cargar la llave privada. ' . $this->obtenerErroresOpenSsl());
        }

        $firma = '';
        $firmado = openssl_sign($contenido, $firma, $llavePrivada, OPENSSL_ALGO_SHA256);

        if (!$firmado) {
            throw new RuntimeException('No se pudo firmar el registro con OpenSSL. ' . $this->obtenerErroresOpenSsl());
        }

        return base64_encode($firma);
    }

    public function verificar(array $datos, string $firmaBase64): bool
    {
        if ($firmaBase64 === '' || !is_file($this->rutaPublica)) {
            return false;
        }

        $contenido = $this->crearContenidoCanonico($datos);
        $llavePublicaContenido = file_get_contents($this->rutaPublica);

        if ($llavePublicaContenido === false) {
            return false;
        }

        $llavePublica = openssl_pkey_get_public($llavePublicaContenido);

        if ($llavePublica === false) {
            return false;
        }

        $firma = base64_decode($firmaBase64, true);

        if ($firma === false) {
            return false;
        }

        return openssl_verify($contenido, $firma, $llavePublica, OPENSSL_ALGO_SHA256) === 1;
    }

    public function crearContenidoCanonico(array $datos): string
    {
        $identidad = mb_strtolower(trim((string) ($datos['identidad'] ?? '')), 'UTF-8');
        $nombre = mb_strtolower(trim((string) ($datos['nombre'] ?? '')), 'UTF-8');
        $apellido = mb_strtolower(trim((string) ($datos['apellido'] ?? '')), 'UTF-8');
        $edad = (string) (int) ($datos['edad'] ?? 0);
        $sexo = trim((string) ($datos['sexo'] ?? ''));
        $paisResidenciaId = (string) (int) ($datos['pais_residencia_id'] ?? 0);
        $nacionalidadId = (string) (int) ($datos['nacionalidad_id'] ?? 0);
        $correo = mb_strtolower(trim((string) ($datos['correo'] ?? '')), 'UTF-8');
        $celular = preg_replace('/\s+/', '', trim((string) ($datos['celular'] ?? ''))) ?? '';

        return implode('|', [
            $identidad,
            $nombre,
            $apellido,
            $edad,
            $sexo,
            $paisResidenciaId,
            $nacionalidadId,
            $correo,
            $celular,
        ]);
    }

    private function asegurarLlaves(): void
    {
        $directorio = dirname($this->rutaPrivada);

        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0775, true) && !is_dir($directorio)) {
                throw new RuntimeException('No se pudo crear la carpeta de llaves: ' . $directorio);
            }
        }

        if (!is_writable($directorio)) {
            throw new RuntimeException('La carpeta de llaves no tiene permisos de escritura: ' . $directorio);
        }

        if (is_file($this->rutaPrivada) && is_file($this->rutaPublica)) {
            return;
        }

        $configuracion = $this->obtenerConfiguracionOpenSsl();

        if (isset($configuracion['config'])) {
            putenv('OPENSSL_CONF=' . $configuracion['config']);
        }

        $recurso = openssl_pkey_new($configuracion);

        if ($recurso === false) {
            throw new RuntimeException('No se pudieron generar las llaves de OpenSSL. ' . $this->obtenerErroresOpenSsl());
        }

        $llavePrivada = '';
        $exportado = openssl_pkey_export($recurso, $llavePrivada, null, $configuracion);

        if (!$exportado || $llavePrivada === '') {
            throw new RuntimeException('No se pudo exportar la llave privada. ' . $this->obtenerErroresOpenSsl());
        }

        $detalles = openssl_pkey_get_details($recurso);

        if ($detalles === false || empty($detalles['key'])) {
            throw new RuntimeException('No se pudo obtener la llave pública. ' . $this->obtenerErroresOpenSsl());
        }

        if (file_put_contents($this->rutaPrivada, $llavePrivada) === false) {
            throw new RuntimeException('No se pudo guardar la llave privada en storage/keys.');
        }

        if (file_put_contents($this->rutaPublica, $detalles['key']) === false) {
            throw new RuntimeException('No se pudo guardar la llave pública en storage/keys.');
        }
    }

    private function obtenerConfiguracionOpenSsl(): array
    {
        $configuracion = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $rutas = [
            getenv('OPENSSL_CONF') ?: '',
            self::OPENSSL_CONFIG_XAMPP,
            'C:/xampp/php/extras/openssl/openssl.cnf',
            'C:/xampp/php/extras/ssl/openssl.cnf',
            'C:/laragon/etc/ssl/openssl.cnf',
        ];

        foreach ($rutas as $ruta) {
            if ($ruta !== '' && is_file($ruta)) {
                $configuracion['config'] = $ruta;
                break;
            }
        }

        return $configuracion;
    }

    private function obtenerErroresOpenSsl(): string
    {
        $errores = [];

        while ($error = openssl_error_string()) {
            $errores[] = $error;
        }

        return empty($errores)
            ? 'No hay detalle adicional de OpenSSL.'
            : implode(' | ', $errores);
    }
}
