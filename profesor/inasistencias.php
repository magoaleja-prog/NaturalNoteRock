<?php
/**
 * profesor/inasistencias.php
 * ---------------------------------------------------------------------
 * Registro de inasistencias por estudiante y materia. Al guardar,
 * Inasistencia::registrar() notifica al estudiante en tiempo real.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['profesor'];
require_once __DIR__ . '/../includes/guard.php';

use App\Models\Inasistencia;
use App\Models\Materia;

/** @var \App\Models\Profesor $usuario */
$materias = (new Materia())->activas();
$estudiantes = $usuario->listarEstudiantes();
$inasistModel = new Inasistencia();

$ok = ''; $error = '';
$tipos = ['ausencia' => 'Ausencia', 'tardanza' => 'Tardanza', 'excusa' => 'Excusa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudianteId = (int) ($_POST['estudiante_id'] ?? 0);
    $materiaId    = (int) ($_POST['materia_id'] ?? 0);
    $fecha        = (string) ($_POST['fecha'] ?? date('Y-m-d'));
    $tipo         = (string) ($_POST['tipo'] ?? 'ausencia');
    $justificada  = isset($_POST['justificada']);
    $observacion  = trim((string) ($_POST['observacion'] ?? ''));

    if ($estudianteId <= 0 || $materiaId <= 0 || !isset($tipos[$tipo])) {
        $error = 'Completa estudiante, materia y tipo de inasistencia.';
    } else {
        $inasistModel->registrar(
            $estudianteId, $materiaId, $usuario->getProfesorId(),
            $fecha, $tipo, $justificada, $observacion ?: null
        );
        $ok = 'Inasistencia registrada y estudiante notificado. 🔔';
    }
}

$recientes = $inasistModel->recientes(15);

$pageTitle = 'Inasistencias';
$activo = url('profesor/inasistencias.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>📋 Registrar inasistencia</h2></div>

<?php if ($ok): ?><div class="alerta alerta-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="estudiante_id">Estudiante</label>
                    <select name="estudiante_id" id="estudiante_id" required>
                        <option value="">— Selecciona —</option>
                        <?php foreach ($estudiantes as $e): ?>
                            <option value="<?= (int) $e['id'] ?>">
                                <?= e($e['apellido'] . ', ' . $e['nombre'] . ' (' . $e['curso'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="materia_id">Materia</label>
                    <select name="materia_id" id="materia_id" required>
                        <option value="">— Selecciona —</option>
                        <?php foreach ($materias as $m): ?>
                            <option value="<?= (int) $m['id'] ?>"><?= e($m['icono'] . ' ' . $m['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="<?= e(date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" id="tipo">
                        <?php foreach ($tipos as $k => $v): ?>
                            <option value="<?= e($k) ?>"><?= e($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="justificada" value="1"
                           style="width:auto;box-shadow:none"> Justificada
                </label>
            </div>
            <div class="form-group">
                <label for="observacion">Observacion</label>
                <textarea name="observacion" id="observacion" rows="2"
                          placeholder="Detalle de la inasistencia..."></textarea>
            </div>
            <button class="btn btn-ambar btn-block">💾 Guardar y notificar</button>
        </form>
    </div>

    <div class="card">
        <h3>🗓️ Inasistencias recientes</h3>
        <?php if (!$recientes): ?>
            <p class="text-dim">Sin registros.</p>
        <?php else: ?>
            <table class="tabla tabla-mini mt">
                <thead><tr><th>Estudiante</th><th>Materia</th><th>Tipo</th><th>Fecha</th></tr></thead>
                <tbody>
                <?php foreach ($recientes as $i): ?>
                    <tr>
                        <td><?= e($i['nombre'] . ' ' . $i['apellido']) ?></td>
                        <td><?= e($i['materia']) ?></td>
                        <td><span class="chip <?= $i['justificada'] ? 'chip-verde' : 'chip-rojo' ?>"><?= e($i['tipo']) ?></span></td>
                        <td class="text-dim"><?= e($i['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
