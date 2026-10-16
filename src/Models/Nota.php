<?php
/**
 * src/Models/Nota.php
 * ---------------------------------------------------------------------
 * Registra calificaciones y DISPARA LA NOTIFICACION automatica al
 * estudiante (sistema de avisos en tiempo real).
 *
 * PILAR POO - ABSTRACCION: quien registra una nota no se preocupa por
 * los avisos; la clase Nota los genera sola.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Support\Jardin;

final class Nota extends Model
{
    protected string $tabla = 'notas';

    public function reglas(): array
    {
        return [
            'valor' => 'La nota debe estar entre 1.0 y 5.0.',
            'periodo' => 'El periodo es obligatorio.',
        ];
    }

    /**
     * Registra una nota y notifica al estudiante.
     *
     * @return int id de la nota creada
     */
    public function registrar(
        int $estudianteId,
        int $materiaId,
        int $profesorId,
        string $periodo,
        float $valor,
        ?string $observacion = null
    ): int {
        $valor = max(1.0, min(5.0, $valor));

        $stmt = $this->db->prepare(
            'INSERT INTO notas (estudiante_id, materia_id, profesor_id, periodo, valor, observacion)
             VALUES (:e, :m, :p, :pe, :v, :o)'
        );
        $stmt->execute([
            ':e' => $estudianteId, ':m' => $materiaId, ':p' => $profesorId,
            ':pe' => $periodo, ':v' => $valor, ':o' => $observacion,
        ]);
        $notaId = (int) $this->db->lastInsertId();

        // --- Notificacion automatica al estudiante --------------------
        $dest = $this->db->prepare('SELECT usuario_id FROM estudiantes WHERE id = :e');
        $dest->execute([':e' => $estudianteId]);
        $usuarioId = (int) $dest->fetchColumn();

        $mat = $this->db->prepare('SELECT nombre FROM materias WHERE id = :m');
        $mat->execute([':m' => $materiaId]);
        $materia = (string) $mat->fetchColumn();

        $jardin = Jardin::paraNota($valor);
        $titulo = sprintf('Nueva nota en %s', $materia);
        $mensaje = sprintf('Tu nota de %s es %.1f. %s', $materia, $valor, $jardin['descripcion']);

        $this->db->prepare(
            'INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url)
             VALUES (:u, :t, :m, "nota", :url)'
        )->execute([
            ':u' => $usuarioId, ':t' => $titulo, ':m' => $mensaje,
            ':url' => 'estudiante/materia.php?id=' . $materiaId,
        ]);

        return $notaId;
    }

    /** Notas de un estudiante con el nombre de la materia. */
    public function deEstudiante(int $estudianteId): array
    {
        $stmt = $this->db->prepare(
            'SELECT n.*, m.nombre AS materia, m.color, m.icono, m.id AS materia_id
               FROM notas n JOIN materias m ON m.id = n.materia_id
              WHERE n.estudiante_id = :e
              ORDER BY n.fecha DESC'
        );
        $stmt->execute([':e' => $estudianteId]);
        return $stmt->fetchAll();
    }

    /** Ultima nota registrada (para tablero). */
    public function ultimas(int $limite = 10): array
    {
        $sql = 'SELECT n.*, m.nombre AS materia, u.nombre, u.apellido
                  FROM notas n
                  JOIN materias m ON m.id = n.materia_id
                  JOIN estudiantes e ON e.id = n.estudiante_id
                  JOIN usuarios u ON u.id = e.usuario_id
                 ORDER BY n.fecha DESC LIMIT ' . max(1, $limite);
        return $this->db->query($sql)->fetchAll();
    }
}
