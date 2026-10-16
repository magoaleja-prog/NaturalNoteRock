<?php
/**
 * includes/guard.php
 * ---------------------------------------------------------------------
 * Bootstrap de toda pagina privada:
 *   1. Carga la configuracion (autoloader, sesion, helpers).
 *   2. Obliga a tener sesion iniciada (regla: nadie navega sin login).
 *
 * Cada pagina define $rolesPermitidos ANTES de incluir este archivo,
 * por ejemplo:  $rolesPermitidos = ['profesor'];
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Support\Auth;

// Si la pagina no declaro roles, exigimos solo estar logueado.
$rolesPermitidos = $rolesPermitidos ?? [];
$usuario = $rolesPermitidos
    ? Auth::requerirRol(...$rolesPermitidos)
    : Auth::requerirLogin();
