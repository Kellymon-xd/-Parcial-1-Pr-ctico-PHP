<?php

declare(strict_types=1);

namespace Itech\Models;

use Itech\Config\Database;

final class Pais
{
    public function obtenerTodos(): array
    {
        return Database::fetchAll(
            'SELECT id, nombre
             FROM paises
             ORDER BY nombre ASC'
        );
    }

    public function obtenerActivos(): array
    {
        return $this->obtenerTodos();
    }

    public function existe(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        return Database::fetchOne(
            'SELECT id
             FROM paises
             WHERE id = :id
             LIMIT 1',
            ['id' => $id]
        ) !== null;
    }
}
