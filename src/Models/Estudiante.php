<?php
/**
 * src/Models/Estudiante.php
 * ---------------------------------------------------------------------
 * Rol estudiante: ve sus materias como PLACAS METALICAS y su nota como
 * un JARDIN VIVO, recibe notificaciones y sube su foto.
 *
 * PILAR POO - HERENCIA/POLIMORFISMO: hereda de Usuario y define su
 * propio menu, permisos y panel.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Estudiante extends Usuario
{
    protected int $estudianteId = 0;
    protected ?string $codigo = null;
    protected ?string $curso = null;
    protected ?string $acudiente = null;
    protected ?string $documento = null;

    protected function hidratarExtra(): void
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare('SELECT * FROM estudiantes WHERE usuario_id = :u LIMIT 1');
        $stmt->execute([':u' => $this->id]);
        if ($fila = $stmt->fetch()) {
            $this->estudianteId = (int) $fila['id'];
            $this->codigo       = $fila['codigo'];
            $this->curso        = $fila['curso'];
            $this->acudiente    = $fila['acudiente'];
            $this->documento    = $fila['documento'];
        }
    }

    public function menu(): array
    {
        return [
            ['titulo' => 'Mi jardin',      'url' => url('estudiante/dashboard.php'),     'icono' => '🌻'],
            ['titulo' => 'Aprendizaje',    'url' => url('estudiante/aprendizaje.php'),   'icono' => '📖'],
            ['titulo' => 'Musica rock',    'url' => url('estudiante/musica.php'),        'icono' => '🎧'],
            ['titulo' => 'Juegos',         'url' => url('estudiante/juegos.php'),        'icono' => '🎮'],
            ['titulo' => 'Mi perfil',      'url' => url('estudiante/perfil.php'),        'icono' => '📸'],
            ['titulo' => 'Notificaciones', 'url' => url('notificaciones.php'),           'icono' => '🔔'],
        ];
    }

    public function panelInicio(): string
    {
        return url('estudiante/dashboard.php');
    }

    public function descripcionRol(): string
    {
        return 'Estudiante: cultiva tu jardin de notas y sube tu foto de perfil.';
    }

    public function permisos(): array
    {
        return ['ver_mis_notas', 'ver_mi_jardin', 'subir_foto', 'editar_perfil', 'jugar'];
    }

    public function puedeGestionarNotas(): bool
    {
        return false;   // el estudiante NO registra notas
    }

    public function puedeVerJardin(): bool
    {
        return true;    // el jardin es exclusivo del estudiante
    }

    public function getEstudianteId(): int { return $this->estudianteId; }
    public function getCodigo(): ?string { return $this->codigo; }
    public function getCurso(): ?string { return $this->curso; }
    public function getAcudiente(): ?string { return $this->acudiente; }
    public function getDocumento(): ?string { return $this->documento; }

    /**
     * Devuelve las 12 materias con la ULTIMA nota del estudiante y el
     * estado del jardin correspondiente. Base de las placas metalicas.
     */
    public function materiasConNotas(): array
    {
        $sql = 'SELECT m.*, n.valor, n.observacion, n.fecha,
                       (SELECT ROUND(AVG(valor),1) FROM notas x
                          WHERE x.estudiante_id = e.id AND x.materia_id = m.id) AS promedio
                FROM materias m
                CROSS JOIN (SELECT id FROM estudiantes WHERE id = :eid) e
                LEFT JOIN notas n ON n.id = (
                    SELECT id FROM notas z
                     WHERE z.estudiante_id = e.id AND z.materia_id = m.id
                     ORDER BY z.fecha DESC, z.id DESC LIMIT 1
                )
                WHERE m.activo = 1
                ORDER BY m.id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':eid' => $this->estudianteId]);
        return $stmt->fetchAll();
    }

    /** Promedio global del estudiante. */
    public function promedioGeneral(): ?float
    {
        $stmt = $this->db->prepare(
            'SELECT ROUND(AVG(valor),1) FROM notas WHERE estudiante_id = :e'
        );
        $stmt->execute([':e' => $this->estudianteId]);
        $valor = $stmt->fetchColumn();
        return $valor !== null ? (float) $valor : null;
    }

    /** Total de inasistencias registradas. */
    public function totalInasistencias(): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM inasistencias WHERE estudiante_id = :e'
        );
        $stmt->execute([':e' => $this->estudianteId]);
        return (int) $stmt->fetchColumn();
    }

    /** Historial de notas de una materia concreta (para el jardin). */
    public function historialMateria(int $materiaId): array
    {
        $stmt = $this->db->prepare(
            'SELECT valor, periodo, observacion, fecha
               FROM notas
              WHERE estudiante_id = :e AND materia_id = :m
              ORDER BY fecha ASC, id ASC'
        );
        $stmt->execute([':e' => $this->estudianteId, ':m' => $materiaId]);
        return $stmt->fetchAll();
    }
}
