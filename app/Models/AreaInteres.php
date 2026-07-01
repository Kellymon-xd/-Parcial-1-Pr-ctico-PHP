<?php

declare(strict_types=1);

namespace Itech\Models;

use Itech\Config\Database;

final class AreaInteres
{
    public function obtenerTodas(): array
    {
        return Database::fetchAll(
            'SELECT id, nombre
             FROM areas_interes
             ORDER BY id ASC'
        );
    }

    public function obtenerActivas(): array
    {
        return $this->obtenerTodas();
    }

    public function existenTodas(array $ids): bool
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if (count($ids) === 0) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $filas = Database::fetchAll(
            "SELECT id
             FROM areas_interes
             WHERE id IN ($placeholders)",
            $ids
        );

        return count($filas) === count($ids);
    }
}
