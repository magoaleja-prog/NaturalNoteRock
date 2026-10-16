<?php
/**
 * src/Models/Profesor.php
 * ---------------------------------------------------------------------
 * Rol docente: registra notas e inasistencias.
 *
 * PILAR POO - HERENCIA/POLIMORFISMO: misma base que Usuario, pero su
 * menu y permisos son propios. NO tiene acceso al jardin del estudiante.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Profesor extends Usuario
{
    protected ?string $especialidad = null;
    protected ?string $titulo = null;
    protected ?string $codigo = null;
    protected int $profesorId = 0;

    protected function hidratarExtra(): void
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare('SELECT * FROM profesores WHERE usuario_id = :u LIMIT 1');
        $stmt->execute([':u' => $this->id]);
        if ($fila = $stmt->fetch()) {
            $this->profesorId  = (int) $fila['id'];
            $this->especialidad = $fila['especialidad'];
            $this->titulo       = $fila['titulo'];
            $this->codigo       = $fila['codigo'];
        }
    }

    public function menu(): array
    {
        return [
            ['titulo' => 'Panel docente',  'url' => url('profesor/dashboard.php'),     'icono' => '🎸'],
            ['titulo' => 'Registrar notas','url' => url('profesor/notas.php'),         'icono' => '🎚️'],
            ['titulo' => 'Inasistencias',  'url' => url('profesor/inasistencias.php'), 'icono' => '📋'],
            ['titulo' => 'Estudiantes',    'url' => url('profesor/estudiantes.php'),   'icono' => '🧑‍🎓'],
            ['titulo' => 'Notificaciones', 'url' => url('notificaciones.php'),         'icono' => '🔔'],
        ];
    }

    public function panelInicio(): string
    {
        return url('profesor/dashboard.php');
    }

    public function descripcionRol(): string
    {
        return 'Profesor: registra notas con la barra deslizante y controla inasistencias.';
    }

    public function permisos(): array
    {
        return ['registrar_notas', 'registrar_inasistencias', 'ver_estudiantes'];
    }

    /**
     * Regla de negocio: el profesor SI gestiona notas pero NUNCA accede
     * al jardin visual del estudiante (eso es privado del estudiante).
     */
    public function puedeGestionarNotas(): bool
    {
        return true;
    }

    public function puedeVerJardin(): bool
    {
        return false;
    }

    // --- Operaciones propias del profesor ----------------------------
    public function getProfesorId(): int { return $this->profesorId; }
    public function getEspecialidad(): ?string { return $this->especialidad; }
    public function getTitulo(): ?string { return $this->titulo; }
    public function getCodigo(): ?string { return $this->codigo; }

    /** Lista los estudiantes a los que puede calificar. */
    public function listarEstudiantes(): array
    {
        $sql = 'SELECT e.id, e.codigo, e.curso, u.nombre, u.apellido, u.foto
                FROM estudiantes e
                JOIN usuarios u ON u.id = e.usuario_id
                ORDER BY e.curso, u.apellido';
        return $this->db->query($sql)->fetchAll();
    }
}
