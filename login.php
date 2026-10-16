<?php
/**
 * login.php
 * ---------------------------------------------------------------------
 * Inicio de sesion OBLIGATORIO. Valida usuario/email + contrasena con
 * password_verify (bcrypt). Al entrar redirige al panel segun el rol
 * (POLIMORFISMO: panelInicio() cambia por subclase).
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

use App\Models\Usuario;
use App\Support\Auth;

// Si ya hay sesion, no tiene sentido volver al login.
$actual = Auth::usuario();
if ($actual !== null) {
    header('Location: ' . $actual->panelInicio());
    exit;
}

$error = '';
$credencial = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credencial = trim((string) ($_POST['credencial'] ?? ''));
    $password   = (string) ($_POST['password'] ?? '');

    if ($credencial === '' || $password === '') {
        $error = 'Debes escribir tu usuario y tu contrasena.';
    } else {
        $usuario = Usuario::buscarPorCredencial($credencial);
        if ($usuario !== null && $usuario->autenticar($password)) {
            Auth::iniciar($usuario);
            header('Location: ' . $usuario->panelInicio());
            exit;
        }
        $error = 'Credenciales incorrectas. Verifica e intenta de nuevo.';
    }
}

if (isset($_GET['error']) && $_GET['error'] === 'sesion') {
    $error = $error ?: 'Tu sesion expiro. Inicia sesion de nuevo.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion · <?= e(APP_NAME) ?></title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="placa login-card">
        <div class="login-logo">🎸 Natural Note Rock 🌿</div>
        <p class="login-sub">
            <?= e(APP_ORG) ?> · Sistema academico rockero y natural
        </p>

        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="credencial">Usuario o correo</label>
                <input type="text" id="credencial" name="credencial"
                       value="<?= e($credencial) ?>" required autofocus
                       placeholder="admin, profe.roca, luna.roca...">
            </div>
            <div class="form-group">
                <label for="password">Contrasena</label>
                <input type="password" id="password" name="password" required
                       placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-verde btn-block">🔓 Entrar al escenario</button>
        </form>

        <p class="hint">
            Cuentas de prueba:<br>
            <code>admin / admin123</code> · <code>profe.roca / profe123</code> ·
            <code>luna.roca / estudiante123</code>
        </p>
    </div>
</div>
</body>
</html>
