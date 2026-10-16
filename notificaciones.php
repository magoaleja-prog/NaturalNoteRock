<?php
/**
 * notificaciones.php
 * ---------------------------------------------------------------------
 * Centro de avisos compartido por los tres roles. Permite marcar como
 * leidas, eliminar una o borrar todas. El contador del menu se
 * actualiza en tiempo real via api/notificaciones.php.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

use App\Models\Notificacion;

$noti = new Notificacion();
$usuarioId = $usuario->getId();
$msg = '';

// Procesar acciones del formulario (respaldo sin JavaScript).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);
    match ($accion) {
        'leer'           => $noti->marcarLeida($id, $usuarioId),
        'leer_todas'     => $noti->marcarTodas($usuarioId),
        'eliminar'       => $noti->eliminar($id, $usuarioId),
        'eliminar_todas' => $noti->eliminarTodas($usuarioId),
        default          => null,
    };
    header('Location: ' . url('notificaciones.php?ok=1'));
    exit;
}

$items = $noti->deUsuario($usuarioId, 50);
$iconos = ['nota' => '🎼', 'inasistencia' => '📋', 'sistema' => '⚙️', 'mensaje' => '✉️'];

$pageTitle = 'Notificaciones';
$activo = url('notificaciones.php');
require __DIR__ . '/includes/header.php';
?>

<div class="titulo-seccion">
    <h2>🔔 Centro de notificaciones</h2>
    <span class="chip chip-ambar"><?= $noti->noLeidas($usuarioId) ?> sin leer</span>
</div>

<div class="flex gap wrap mb">
    <form method="post" style="display:inline">
        <input type="hidden" name="action" value="leer_todas">
        <button class="btn btn-sm btn-verde">✔ Marcar todas como leidas</button>
    </form>
    <form method="post" style="display:inline"
          onsubmit="return confirm('¿Eliminar TODAS tus notificaciones?');">
        <input type="hidden" name="action" value="eliminar_todas">
        <button class="btn btn-sm btn-rojo">🗑 Eliminar todas</button>
    </form>
</div>

<?php if (!$items): ?>
    <div class="card text-center text-dim">
        <p style="font-size:2rem">🌙</p>
        <p>No tienes notificaciones por ahora. ¡Rockea tranquilo!</p>
    </div>
<?php else: ?>
    <?php foreach ($items as $n): ?>
        <div class="notif-item <?= $n['leida'] ? '' : 'no-leida' ?>">
            <div class="ico"><?= $iconos[$n['tipo']] ?? '🔔' ?></div>
            <div style="flex:1">
                <strong><?= e($n['titulo']) ?></strong>
                <div class="text-soft"><?= e($n['mensaje']) ?></div>
                <div class="fecha"><?= e($n['creado_en']) ?></div>
                <?php if (!empty($n['url'])): ?>
                    <a class="btn btn-sm btn-ghost" href="<?= e(url($n['url'])) ?>">Ver detalle →</a>
                <?php endif; ?>
            </div>
            <div class="notif-acciones">
                <?php if (!$n['leida']): ?>
                    <form method="post">
                        <input type="hidden" name="action" value="leer">
                        <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                        <button class="btn btn-sm">✔</button>
                    </form>
                <?php endif; ?>
                <form method="post" onsubmit="return confirm('¿Eliminar esta notificacion?');">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                    <button class="btn btn-sm btn-rojo">🗑</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
