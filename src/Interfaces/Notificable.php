<?php
/**
 * src/Interfaces/Notificable.php
 * ---------------------------------------------------------------------
 * Contrato para quienes pueden enviar y recibir notificaciones.
 *
 * PILAR POO - ABSTRACCION:
 *   El sistema de avisos no necesita saber si el destinatario es un
 *   estudiante o un profesor; solo sabe que "es Notificable".
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\Interfaces;

interface Notificable
{
    /** Crea una notificacion para este usuario. Devuelve el id creado. */
    public function notificar(string $titulo, string $mensaje, string $tipo = 'sistema', ?string $url = null): int;

    /** Devuelve todas las notificaciones de este usuario. */
    public function misNotificaciones(int $limite = 30): array;

    /** Cuenta las notificaciones sin leer (para el contador del menu). */
    public function notificacionesNoLeidas(): int;
}
