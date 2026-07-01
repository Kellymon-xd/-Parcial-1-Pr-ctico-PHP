<?php

declare(strict_types=1);

namespace Itech\Config;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private const HOST = 'localhost';
    private const DB_NAME = 'parcial_itech';
    private const USER = 'itech_app';
    private const PASSWORD = 'ItechApp2026*';
    private const CHARSET = 'utf8mb4';

    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::HOST,
                self::DB_NAME,
                self::CHARSET
            );

            try {
                self::$connection = new PDO($dsn, self::USER, self::PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                throw new RuntimeException(
                    'Error de conexión. Importa database/parcial_itech.sql y verifica que exista el usuario itech_app.',
                    0,
                    $exception
                );
            }
        }

        return self::$connection;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $statement = self::getConnection()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = self::getConnection()->prepare($sql);
        $statement->execute($params);

        $result = $statement->fetch();

        return $result === false ? null : $result;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $statement = self::getConnection()->prepare($sql);

        return $statement->execute($params);
    }

    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
}
