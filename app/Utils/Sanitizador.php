<?php

declare(strict_types=1);

namespace Itech\Utils;

final class Sanitizador
{
    public static function texto(?string $valor): string
    {
        $valor = trim((string) $valor);
        $valor = strip_tags($valor);
        $valor = preg_replace('/\s+/u', ' ', $valor) ?? '';

        return trim($valor);
    }

    public static function html(?string $valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function correo(?string $valor): string
    {
        return mb_strtolower(self::texto($valor), 'UTF-8');
    }

    public static function titulo(?string $valor): string
    {
        $texto = self::texto($valor);
        $texto = mb_strtolower($texto, 'UTF-8');

        return mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');
    }

    public static function telefono(?string $valor): string
    {
        $valor = self::texto($valor);

        return preg_replace('/[^0-9+() -]/', '', $valor) ?? '';
    }
}
