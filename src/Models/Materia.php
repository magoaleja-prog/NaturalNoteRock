<?php
/**
 * src/Models/Materia.php
 * ---------------------------------------------------------------------
 * Representa una de las 12 areas academicas. Alimenta las placas
 * metalicas del estudiante con su color e icono.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Materia extends Model
{
    protected string $tabla = 'materias';

    public function reglas(): array
    {
        return [
            'nombre' => 'El nombre de la materia es obligatorio.',
            'codigo' => 'El codigo de la materia es obligatorio.',
        ];
    }

    /** Materias activas (para selects y placas). */
    public function activas(): array
    {
        return $this->db->query(
            'SELECT * FROM materias WHERE activo = 1 ORDER BY nombre'
        )->fetchAll();
    }

    public function crear(string $nombre, string $codigo, string $area, string $descripcion, string $color, string $icono): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO materias (nombre, codigo, area, descripcion, color, icono)
             VALUES (:n, :c, :a, :d, :co, :i)'
        );
        $stmt->execute([
            ':n' => $nombre, ':c' => $codigo, ':a' => $area,
            ':d' => $descripcion, ':co' => $color, ':i' => $icono,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE materias SET nombre=:n, codigo=:c, area=:a,
                    descripcion=:d, color=:co, icono=:i WHERE id=:id'
        );
        return $stmt->execute([
            ':n' => $d['nombre'], ':c' => $d['codigo'], ':a' => $d['area'],
            ':d' => $d['descripcion'], ':co' => $d['color'], ':i' => $d['icono'],
            ':id' => $id,
        ]);
    }

    /** Promedio de una materia en todo el colegio (para reportes). */
    public function promedioGeneral(int $materiaId): ?float
    {
        $stmt = $this->db->prepare('SELECT ROUND(AVG(valor),1) FROM notas WHERE materia_id = :m');
        $stmt->execute([':m' => $materiaId]);
        $v = $stmt->fetchColumn();
        return $v !== null ? (float) $v : null;
    }
}
