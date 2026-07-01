<?php

declare(strict_types=1);

namespace Itech\Utils;

final class Validador
{
    public static function requerido(string $valor): bool
    {
        return trim($valor) !== '';
    }

    public static function identidad(string $valor): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9\-]{4,20}$/', $valor);
    }

    public static function nombre(string $valor): bool
    {
        return (bool) preg_match("/^[\p{L}][\p{L}\s'.-]{1,99}$/u", $valor);
    }

    public static function edad($valor): bool
    {
        $edad = filter_var($valor, FILTER_VALIDATE_INT);

        return $edad !== false && $edad >= 1 && $edad <= 120;
    }

    public static function sexo(string $valor): bool
    {
        return in_array($valor, ['Masculino', 'Femenino', 'Otro'], true);
    }

    public static function id($valor): bool
    {
        $id = filter_var($valor, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0;
    }

    public static function correo(string $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false && strlen($valor) <= 150;
    }

    public static function celular(string $valor): bool
    {
        return (bool) preg_match('/^[0-9+() -]{7,20}$/', $valor);
    }

    public static function areas(array $areas): bool
    {
        if (count($areas) === 0) {
            return false;
        }

        foreach ($areas as $area) {
            if (!self::id($area)) {
                return false;
            }
        }

        return true;
    }

    public static function longitudMaxima(string $valor, int $maximo): bool
    {
        return mb_strlen($valor, 'UTF-8') <= $maximo;
    }
}
