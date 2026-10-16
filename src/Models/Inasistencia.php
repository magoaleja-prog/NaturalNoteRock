<?php
/**
 * src/Models/Inasistencia.php
 * ---------------------------------------------------------------------
 * Registra ausencias/tardanzas/excusas y notifica al estudiante.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Inasistencia extends Model
{
    protected string $tabla = 'inasistencias';

    public function reglas(): array
    {
        return ['fecha' => 'La fecha es obligatoria.'];
    }

    public function registrar(
        int $estudianteId,
        int $materiaId,
        int $profesorId,
        string $fecha,
        string $tipo = 'ausencia',
        bool $justificada = false,
        ?string $observacion = null
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO inasistencias
                (estudiante_id, materia_id, profesor_id, fecha, tipo, justificada, observacion)
             VALUES (:e, :m, :p, :f, :t, :j, :o)'
        );
        $stmt->execute([
            ':e' => $estudianteId, ':m' => $materiaId, ':p' => $profesorId,
            ':f' => $fecha, ':t' => $tipo, ':j' => $justificada ? 1 : 0,
            ':o' => $observacion,
        ]);
        $id = (int) $this->db->lastInsertId();

        // Notificacion al estudiante.
        $dest = $this->db->prepare('SELECT usuario_id FROM estudiantes WHERE id = :e');
        $dest->execute([':e' => $estudianteId]);
        $usuarioId = (int) $dest->fetchColumn();

        $mat = $this->db->prepare('SELECT nombre FROM materias WHERE id = :m');
        $mat->execute([':m' => $materiaId]);
        $materia = (string) $mat->fetchColumn();

        $etiqueta = ['ausencia' => 'una ausencia', 'tardanza' => 'una tardanza', 'excusa' => 'una excusa'][$tipo] ?? $tipo;
        $this->db->prepare(
            'INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url)
             VALUES (:u, :t, :m, "inasistencia", :url)'
        )->execute([
            ':u' => $usuarioId,
            ':t' => 'Inasistencia en ' . $materia,
            ':m' => sprintf('Se registro %s el %s.%s', $etiqueta, $fecha,
                        $justificada ? ' (justificada)' : ''),
            ':url' => 'estudiante/dashboard.php',
        ]);

        return $id;
    }

    public function deEstudiante(int $estudianteId): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, m.nombre AS materia, m.color, m.icono
               FROM inasistencias i JOIN materias m ON m.id = i.materia_id
              WHERE i.estudiante_id = :e ORDER BY i.fecha DESC'
        );
        $stmt->execute([':e' => $estudianteId]);
        return $stmt->fetchAll();
    }

    public function recientes(int $limite = 10): array
    {
        $sql = 'SELECT i.*, m.nombre AS materia, u.nombre, u.apellido
                  FROM inasistencias i
                  JOIN materias m ON m.id = i.materia_id
                  JOIN estudiantes e ON e.id = i.estudiante_id
                  JOIN usuarios u ON u.id = e.usuario_id
                 ORDER BY i.fecha DESC LIMIT ' . max(1, $limite);
        return $this->db->query($sql)->fetchAll();
    }
}
