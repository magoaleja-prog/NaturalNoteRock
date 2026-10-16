<?php
/**
 * api/notificaciones.php
 * ---------------------------------------------------------------------
 * API JSON para el sistema de avisos en tiempo real.
 *
 *   GET  -> { noLeidas, items: [...] }        (polling del menu)
 *   POST action=leer         & id=..           marcar una como leida
 *   POST action=leer_todas                     marcar todas
 *   POST action=eliminar     & id=..           borrar una
 *   POST action=eliminar_todas                 borrar todas
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Models\Notificacion;
use App\Support\Auth;

header('Content-Type: application/json; charset=utf-8');

$usuario = Auth::usuario();
if ($usuario === null) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$noti = new Notificacion();
$usuarioId = $usuario->getId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    switch ($accion) {
        case 'leer':
            $noti->marcarLeida($id, $usuarioId);
            break;
        case 'leer_todas':
            $noti->marcarTodas($usuarioId);
            break;
        case 'eliminar':
            $noti->eliminar($id, $usuarioId);
            break;
        case 'eliminar_todas':
            $noti->eliminarTodas($usuarioId);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Accion no valida']);
            exit;
    }
}

// Respuesta comun: contador + ultimas 15 notificaciones.
$items = array_map(static function (array $n): array {
    return [
        'id'      => (int) $n['id'],
        'titulo'  => $n['titulo'],
        'mensaje' => $n['mensaje'],
        'tipo'    => $n['tipo'],
        'url'     => $n['url'],
        'leida'   => (bool) $n['leida'],
        'fecha'   => $n['creado_en'],
    ];
}, $noti->deUsuario($usuarioId, 15));

echo json_encode([
    'noLeidas' => $noti->noLeidas($usuarioId),
    'items'    => $items,
]);
