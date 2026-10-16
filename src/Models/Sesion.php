<?php
/**
 * src/Models/Sesion.php
 * ---------------------------------------------------------------------
 * Auditoria de accesos. Cada inicio de sesion crea una fila con un
 * token aleatorio, la IP y el navegador. Refuerza la regla de que
 * TODOS deben loguearse.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Sesion extends Model
{
    protected string $tabla = 'sesiones';

    public function reglas(): array
    {
        return ['token' => 'El token de sesion es obligatorio.'];
    }

    public function registrar(int $usuarioId, string $ip, string $userAgent): string
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare(
            'INSERT INTO sesiones (usuario_id, token, ip, user_agent, activa)
             VALUES (:u, :t, :i, :a, 1)'
        );
        $stmt->execute([
            ':u' => $usuarioId, ':t' => $token,
            ':i' => substr($ip, 0, 45), ':a' => substr($userAgent, 0, 255),
        ]);
        return $token;
    }

    public function cerrarPorUsuario(int $usuarioId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE sesiones SET activa = 0, cierre_en = NOW()
              WHERE usuario_id = :u AND activa = 1'
        );
        $stmt->execute([':u' => $usuarioId]);
    }

    public function activas(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM sesiones WHERE activa = 1')->fetchColumn();
    }

    public function ultimas(int $limite = 20): array
    {
        $sql = 'SELECT s.*, u.nombre, u.apellido, u.rol
                  FROM sesiones s JOIN usuarios u ON u.id = s.usuario_id
                 ORDER BY s.creado_en DESC LIMIT ' . max(1, $limite);
        return $this->db->query($sql)->fetchAll();
    }
}
