<?php

declare(strict_types=1);

namespace Itech\Models;

use Itech\Config\Database;
use Itech\Services\FirmaDigitalService;
use Throwable;

final class Inscriptor
{
    public function guardar(array $datos, array $areas): int
    {
        $pdo = Database::getConnection();
        $firmaService = new FirmaDigitalService();
        $firmaDigital = $firmaService->firmar($datos);

        try {
            $pdo->beginTransaction();

            $sql = 'INSERT INTO inscriptores
                    (nombre, apellido, edad, sexo, pais_residencia_id, nacionalidad_id, correo, celular, observaciones)
                    VALUES
                    (:nombre, :apellido, :edad, :sexo, :pais_residencia_id, :nacionalidad_id, :correo, :celular, :observaciones)';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'edad' => $datos['edad'],
                'sexo' => $datos['sexo'],
                'pais_residencia_id' => $datos['pais_residencia_id'],
                'nacionalidad_id' => $datos['nacionalidad_id'],
                'correo' => $datos['correo'],
                'celular' => $datos['celular'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $idInscriptor = (int) $pdo->lastInsertId();

            $statementTema = $pdo->prepare(
                'INSERT INTO inscriptor_temas
                 (inscriptor_id, area_interes_id)
                 VALUES
                 (:inscriptor_id, :area_interes_id)'
            );

            foreach (array_unique(array_map('intval', $areas)) as $idArea) {
                if ($idArea <= 0) {
                    continue;
                }

                $statementTema->execute([
                    'inscriptor_id' => $idInscriptor,
                    'area_interes_id' => $idArea,
                ]);
            }

            $statementFirma = $pdo->prepare(
                'INSERT INTO firmas_digitales
                 (inscriptor_id, firma_digital)
                 VALUES
                 (:inscriptor_id, :firma_digital)'
            );

            $statementFirma->execute([
                'inscriptor_id' => $idInscriptor,
                'firma_digital' => $firmaDigital,
            ]);

            $pdo->commit();

            return $idInscriptor;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function obtenerReporte(): array
    {
        $sql = "SELECT
                    i.id,
                    i.nombre,
                    i.apellido,
                    i.edad,
                    i.sexo,
                    i.pais_residencia_id,
                    i.nacionalidad_id,
                    pr.nombre AS pais_residencia,
                    pn.nombre AS nacionalidad,
                    i.correo,
                    i.celular,
                    i.observaciones,
                    i.fecha_registro,
                    COALESCE(fd.firma_digital, '') AS firma_digital,
                    fd.algoritmo,
                    fd.fecha_firma,
                    GROUP_CONCAT(ai.nombre ORDER BY ai.id SEPARATOR ', ') AS temas
                FROM inscriptores i
                INNER JOIN paises pr
                    ON pr.id = i.pais_residencia_id
                INNER JOIN paises pn
                    ON pn.id = i.nacionalidad_id
                LEFT JOIN firmas_digitales fd
                    ON fd.inscriptor_id = i.id
                LEFT JOIN inscriptor_temas it
                    ON it.inscriptor_id = i.id
                LEFT JOIN areas_interes ai
                    ON ai.id = it.area_interes_id
                GROUP BY
                    i.id,
                    i.nombre,
                    i.apellido,
                    i.edad,
                    i.sexo,
                    i.pais_residencia_id,
                    i.nacionalidad_id,
                    pr.nombre,
                    pn.nombre,
                    i.correo,
                    i.celular,
                    i.observaciones,
                    i.fecha_registro,
                    fd.firma_digital,
                    fd.algoritmo,
                    fd.fecha_firma
                ORDER BY i.fecha_registro DESC";

        return Database::fetchAll($sql);
    }
}
