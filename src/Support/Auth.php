<?php
/**
 * src/Support/Auth.php
 * ---------------------------------------------------------------------
 * Fachada de autenticacion. Centraliza el manejo de la sesion PHP y
 * obliga a que TODOS los usuarios esten logueados.
 *
 * PILAR POO - ABSTRACCION + POLIMORFISMO:
 *   Auth siempre devuelve un objeto App\Models\Usuario (que en la
 *   practica es un Administrador, Profesor o Estudiante). El resto del
 *   sistema lo usa de forma polimorfica sin preguntar por la clase real.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Support;

use App\Models\Usuario;
use App\Models\Sesion;

final class Auth
{
    /**
     * Inicia sesion: guarda el id en $_SESSION y registra la sesion
     * en la base de datos (tabla Sesiones).
     */
    public static function iniciar(Usuario $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['rol']        = $usuario->getRol();
        $_SESSION['nombre']     = $usuario->getNombreCompleto();

        $usuario->marcarAcceso();

        (new Sesion())->registrar(
            $usuario->getId(),
            $_SERVER['REMOTE_ADDR'] ?? 'local',
            $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido'
        );
    }

    /** Reconstruye el objeto Usuario (subclase correcta) desde la sesion. */
    public static function usuario(): ?Usuario
    {
        $id = $_SESSION['usuario_id'] ?? null;
        if ($id === null) {
            return null;
        }
        return Usuario::buscarPorId((int) $id);
    }

    /** true si hay alguien logueado. */
    public static function check(): bool
    {
        return isset($_SESSION['usuario_id']) && self::usuario() !== null;
    }

    /**
     * Puerta de entrada: si no hay sesion, redirige al login.
     * Se llama al inicio de TODAS las paginas privadas.
     */
    public static function requerirLogin(): Usuario
    {
        $usuario = self::usuario();
        if ($usuario === null) {
            header('Location: ' . url('login.php?error=sesion'));
            exit;
        }
        return $usuario;
    }

    /**
     * Exige que el usuario tenga uno de los roles indicados.
     * Protege cada modulo (admin / profesor / estudiante).
     */
    public static function requerirRol(string ...$roles): Usuario
    {
        $usuario = self::requerirLogin();
        if (!in_array($usuario->getRol(), $roles, true)) {
            http_response_code(403);
            header('Location: ' . $usuario->panelInicio() . '?error=permiso');
            exit;
        }
        return $usuario;
    }

    /** Cierra la sesion y marca la fila de Sesiones como cerrada. */
    public static function cerrar(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            (new Sesion())->cerrarPorUsuario((int) $_SESSION['usuario_id']);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }
        session_destroy();
    }
}
