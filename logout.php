<?php
/**
 * logout.php
 * ---------------------------------------------------------------------
 * Cierra la sesion (destruye $_SESSION y marca la sesion como cerrada
 * en la base de datos) y devuelve al login.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

use App\Support\Auth;

Auth::cerrar();
header('Location: ' . url('login.php'));
exit;
