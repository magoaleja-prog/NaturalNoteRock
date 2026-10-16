<?php
/**
 * src/Models/Notificacion.php
 * ---------------------------------------------------------------------
 * Operaciones sobre los avisos: listar, contar, marcar leidas y
 * eliminar. Alimenta el contador del menu y el panel de avisos.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Notificacion extends Model
{
    protected string $tabla = 'notificaciones';

    public function reglas(): array
    {
        return ['titulo' => 'El titulo es obligatorio.'];
    }

    public function deUsuario(int $usuarioId, int $limite = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notificaciones WHERE usuario_id = :u
             ORDER BY leida ASC, creado_en DESC LIMIT ' . max(1, $limite)
        );
        $stmt->execute([':u' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function noLeidas(int $usuarioId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :u AND leida = 0'
        );
        $stmt->execute([':u' => $usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    /** Marca una notificacion como leida (solo si es del usuario). */
    public function marcarLeida(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notificaciones SET leida = 1 WHERE id = :id AND usuario_id = :u'
        );
        return $stmt->execute([':id' => $id, ':u' => $usuarioId]);
    }

    public function marcarTodas(int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notificaciones SET leida = 1 WHERE usuario_id = :u AND leida = 0'
        );
        return $stmt->execute([':u' => $usuarioId]);
    }

    /** Elimina una notificacion del usuario. */
    public function eliminar(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM notificaciones WHERE id = :id AND usuario_id = :u'
        );
        return $stmt->execute([':id' => $id, ':u' => $usuarioId]);
    }

    public function eliminarTodas(int $usuarioId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM notificaciones WHERE usuario_id = :u');
        return $stmt->execute([':u' => $usuarioId]);
    }
}
