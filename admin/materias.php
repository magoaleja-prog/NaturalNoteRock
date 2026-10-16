<?php
/**
 * admin/materias.php
 * ---------------------------------------------------------------------
 * CRUD de materias. El color y el icono alimentan las placas metalicas
 * del estudiante.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['administrador'];
require_once __DIR__ . '/../includes/guard.php';

use App\Models\Materia;

/** @var \App\Models\Administrador $usuario */
$modelo = new Materia();
$ok = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear' || $accion === 'editar') {
        $datos = [
            'nombre'      => trim((string) ($_POST['nombre'] ?? '')),
            'codigo'      => strtoupper(trim((string) ($_POST['codigo'] ?? ''))),
            'area'        => trim((string) ($_POST['area'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'color'       => (string) ($_POST['color'] ?? '#3a3f4a'),
            'icono'       => trim((string) ($_POST['icono'] ?? '🎸')),
        ];
        if ($datos['nombre'] === '' || $datos['codigo'] === '') {
            $error = 'Nombre y codigo son obligatorios.';
        } else {
            try {
                if ($accion === 'crear') {
                    $modelo->crear($datos['nombre'], $datos['codigo'], $datos['area'], $datos['descripcion'], $datos['color'], $datos['icono']);
                    $ok = 'Materia creada.';
                } else {
                    $modelo->actualizar((int) $_POST['id'], $datos);
                    $ok = 'Materia actualizada.';
                }
            } catch (Throwable $ex) {
                $error = 'Error: ' . $ex->getMessage();
            }
        }
    }

    if ($accion === 'toggle') {
        $modelo->conexion()->prepare('UPDATE materias SET activo = 1 - activo WHERE id = :id')
            ->execute([':id' => (int) $_POST['id']]);
        $ok = 'Estado de la materia actualizado.';
    }

    if ($accion === 'eliminar') {
        $modelo->delete((int) $_POST['id']);
        $ok = 'Materia eliminada.';
    }
}

$editando = null;
if (isset($_GET['editar'])) {
    $editando = $modelo->find((int) $_GET['editar']);
}
$materias = $modelo->all('nombre');

$pageTitle = 'Materias';
$activo = url('admin/materias.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>📚 Gestion de materias</h2>
    <span class="chip chip-verde"><?= count($materias) ?> materias</span>
</div>

<?php if ($ok): ?><div class="alerta alerta-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="card mb">
    <h3><?= $editando ? '✏️ Editar materia' : '➕ Nueva materia' ?></h3>
    <form method="post" class="mt">
        <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $editando['id'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label>Nombre</label>
                <input name="nombre" value="<?= e($editando['nombre'] ?? '') ?>" required></div>
            <div class="form-group"><label>Codigo</label>
                <input name="codigo" value="<?= e($editando['codigo'] ?? '') ?>" maxlength="20" required></div>
            <div class="form-group"><label>Area</label>
                <input name="area" value="<?= e($editando['area'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Color de la placa</label>
                <input type="color" name="color" value="<?= e($editando['color'] ?? '#3a3f4a') ?>"
                       style="height:44px;padding:.2rem"></div>
            <div class="form-group"><label>Icono (emoji)</label>
                <input name="icono" value="<?= e($editando['icono'] ?? '🎸') ?>" maxlength="6"></div>
        </div>
        <div class="form-group"><label>Descripcion</label>
            <textarea name="descripcion" rows="2"><?= e($editando['descripcion'] ?? '') ?></textarea></div>
        <div class="flex gap">
            <button class="btn btn-verde">💾 <?= $editando ? 'Actualizar' : 'Crear' ?></button>
            <?php if ($editando): ?>
                <a class="btn btn-ghost" href="<?= url('admin/materias.php') ?>">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <table class="tabla">
        <thead><tr><th>Placa</th><th>Nombre</th><th>Codigo</th><th>Area</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($materias as $m): ?>
            <tr>
                <td>
                    <span class="placa" style="--mat: <?= e($m['color']) ?>;display:inline-grid;place-items:center;width:44px;height:44px;padding:0">
                        <?= $m['icono'] ?>
                    </span>
                </td>
                <td><strong><?= e($m['nombre']) ?></strong></td>
                <td class="text-dim"><?= e($m['codigo']) ?></td>
                <td class="text-soft"><?= e($m['area']) ?></td>
                <td><span class="chip <?= $m['activo'] ? 'chip-verde' : 'chip-rojo' ?>"><?= $m['activo'] ? 'activa' : 'inactiva' ?></span></td>
                <td>
                    <div class="flex gap">
                        <a class="btn btn-sm" href="<?= url('admin/materias.php?editar=' . (int) $m['id']) ?>">✏️</a>
                        <form method="post">
                            <input type="hidden" name="accion" value="toggle">
                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                            <button class="btn btn-sm"><?= $m['activo'] ? '⏸' : '▶' ?></button>
                        </form>
                        <form method="post" onsubmit="return confirm('¿Eliminar la materia y sus notas?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                            <button class="btn btn-sm btn-rojo">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
