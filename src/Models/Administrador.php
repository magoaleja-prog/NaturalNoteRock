<?php
/**
 * src/Models/Administrador.php
 * ---------------------------------------------------------------------
 * Rol con control total del sistema.
 *
 * PILAR POO - HERENCIA: extiende Usuario y reutiliza su conexion,
 * login y notificaciones.
 * PILAR POO - POLIMORFISMO: redefine menu(), permisos() y
 * descripcionRol() con su propio comportamiento.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Models;

final class Administrador extends Usuario
{
    /** Menu exclusivo del administrador. */
    public function menu(): array
    {
        return [
            ['titulo' => 'Panel general', 'url' => url('admin/dashboard.php'), 'icono' => '🎛️'],
            ['titulo' => 'Usuarios',      'url' => url('admin/usuarios.php'),  'icono' => '👥'],
            ['titulo' => 'Materias',      'url' => url('admin/materias.php'),  'icono' => '📚'],
            ['titulo' => 'Reportes',      'url' => url('admin/reportes.php'),  'icono' => '📈'],
            ['titulo' => 'Notificaciones','url' => url('notificaciones.php'),  'icono' => '🔔'],
        ];
    }

    public function panelInicio(): string
    {
        return url('admin/dashboard.php');
    }

    public function descripcionRol(): string
    {
        return 'Administrador: gobierno total del sistema, gestion de usuarios y materias.';
    }

    public function permisos(): array
    {
        return [
            'gestionar_usuarios', 'gestionar_materias', 'ver_reportes',
            'eliminar_cualquiera', 'ver_todos_los_paneles',
        ];
    }

    /** El admin puede gestionar notas (aunque normalmente no lo hace). */
    public function puedeGestionarNotas(): bool
    {
        return true;
    }
}
