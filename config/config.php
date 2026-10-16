<?php
/**
 * config/config.php
 * ---------------------------------------------------------------------
 * Configuracion global de Natural Note Rock.
 *
 * - Define constantes de conexion y rutas.
 * - Registra un autoloader simple (PSR-4 casero) para las clases App\*.
 * - Arranca la sesion de PHP (obligatoria: nadie navega sin loguearse).
 * - Expone los helpers globales url() y asset().
 *
 * Principio POO aplicado: ABSTRACCION. Este archivo esconde todos los
 * detalles de infraestructura; el resto del codigo solo usa la API que
 * exponen las clases y los helpers.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

// -------------------------------------------------------------------
// 1. Rutas del proyecto
// -------------------------------------------------------------------
define('APP_NAME', 'Natural Note Rock');
define('APP_ORG',  'UCO');
define('APP_FOLDER', 'NaturalNoteRock');   // nombre de la carpeta en htdocs
define('APP_ROOT', dirname(__DIR__));      // ruta fisica del proyecto
define('UPLOAD_DIR', APP_ROOT . '/uploads/avatares');
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2 MB para fotos de perfil

// -------------------------------------------------------------------
// 2. Parametros de la base de datos (COMPARTIDOS con Java)
//    Se leen del archivo database/db.properties para que PHP y Java
//    usen exactamente la misma conexion.
// -------------------------------------------------------------------
$__props = [];
$__propsFile = APP_ROOT . '/database/db.properties';
if (is_file($__propsFile)) {
    foreach (file($__propsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $__linea) {
        $__linea = trim($__linea);
        if ($__linea === '' || $__linea[0] === '#' || $__linea[0] === ';') {
            continue;
        }
        $__par = explode('=', $__linea, 2);
        $__props[trim($__par[0])] = isset($__par[1]) ? trim($__par[1]) : '';
    }
    unset($__linea, $__par);
}
unset($__propsFile);

define('DB_HOST', $__props['db.host'] ?? '127.0.0.1');
define('DB_PORT', $__props['db.port'] ?? '3306');
define('DB_NAME', $__props['db.name'] ?? 'natural_note_rock');
define('DB_USER', $__props['db.user'] ?? 'root');
define('DB_PASS', $__props['db.password'] ?? '');
define('DB_CHARSET', $__props['db.charset'] ?? 'utf8mb4');
unset($__props);

// -------------------------------------------------------------------
// 3. Deteccion automatica de la URL base (funciona desde subcarpetas)
// -------------------------------------------------------------------
$__script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
$__pos    = strpos($__script, '/' . APP_FOLDER);

if ($__pos !== false) {
    // Nos quedamos con todo lo anterior al nombre de la carpeta, mas la carpeta.
    // Ej: "/NaturalNoteRock/login.php" -> "/NaturalNoteRock"
    $__base = substr($__script, 0, $__pos + strlen(APP_FOLDER) + 1);
} else {
    $__base = '';
}

// Nos aseguramos SIEMPRE de terminar con "/" (si no, url()/asset()
// generan rutas rotas como "/NaturalNoteRocklogin.php" o "/NaturalNoteRockassets/...").
$__base = rtrim($__base, '/') . '/';
if ($__base === '//') {
    $__base = '/';
}

define('BASE_PATH', $__base);
unset($__script, $__pos, $__base);

/** Devuelve la URL absoluta de una ruta interna del proyecto. */
function url(string $path = ''): string
{
    return BASE_PATH . ltrim($path, '/');
}

/** Devuelve la URL de un recurso estatico (css, js, img, audio). */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Escapa texto para mostrarlo seguro en HTML. */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// -------------------------------------------------------------------
// 4. Autoloader: convierte App\Models\Nota  ->  src/Models/Nota.php
// -------------------------------------------------------------------
spl_autoload_register(static function (string $clase): void {
    $prefijo = 'App\\';
    if (strncmp($prefijo, $clase, strlen($prefijo)) !== 0) {
        return;
    }
    $relativa = str_replace('\\', '/', substr($clase, strlen($prefijo)));
    $archivo  = APP_ROOT . '/src/' . $relativa . '.php';
    if (is_file($archivo)) {
        require_once $archivo;
    }
});

// -------------------------------------------------------------------
// 5. Zona horaria y sesion segura
// -------------------------------------------------------------------
date_default_timezone_set('America/Bogota');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
