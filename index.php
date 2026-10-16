<?php
/**
 * index.php
 * ---------------------------------------------------------------------
 * Puerta de entrada. Nadie ve nada sin loguearse:
 *   - Si hay sesion activa -> al panel del rol.
 *   - Si no -> al formulario de login.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

use App\Support\Auth;

$usuario = Auth::usuario();
if ($usuario !== null) {
    header('Location: ' . $usuario->panelInicio());
    exit;
}
header('Location: ' . url('login.php'));
exit;
