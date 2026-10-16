<?php
/**
 * profesor/notas.php
 * ---------------------------------------------------------------------
 * Registro de notas con BARRA DESLIZANTE ANIMADA (no se escribe el
 * numero). Al guardar, Nota::registrar() notifica al estudiante.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['profesor'];
require_once __DIR__ . '/../includes/guard.php';

use App\Models\Materia;
use App\Models\Nota;

/** @var \App\Models\Profesor $usuario */
$materiaModel = new Materia();
$notaModel = new Nota();
$materias = $materiaModel->activas();
$estudiantes = $usuario->listarEstudiantes();
$periodos = ['Periodo 1', 'Periodo 2', 'Periodo 3', 'Periodo 4'];

$ok = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudianteId = (int) ($_POST['estudiante_id'] ?? 0);
    $materiaId    = (int) ($_POST['materia_id'] ?? 0);
    $periodo      = (string) ($_POST['periodo'] ?? 'Periodo 1');
    $valor        = (float) ($_POST['valor'] ?? 0);
    $observacion  = trim((string) ($_POST['observacion'] ?? ''));

    if ($estudianteId <= 0 || $materiaId <= 0 || $valor < 1 || $valor > 5) {
        $error = 'Selecciona estudiante, materia y una nota valida (1.0 - 5.0).';
    } else {
        $notaModel->registrar(
            $estudianteId, $materiaId, $usuario->getProfesorId(),
            $periodo, $valor, $observacion ?: null
        );
        $ok = sprintf('Nota %.1f registrada y estudiante notificado. 🔔', $valor);
    }
}

$ultimas = $notaModel->ultimas(12);

$pageTitle = 'Registrar notas';
$activo = url('profesor/notas.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>🎚️ Registrar nota</h2></div>

<?php if ($ok): ?><div class="alerta alerta-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <form method="post" id="formNota">
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
                    <label for="periodo">Periodo</label>
                    <select name="periodo" id="periodo">
                        <?php foreach ($periodos as $p): ?>
                            <option><?= e($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="observacion">Observacion (opcional)</label>
                <textarea name="observacion" id="observacion" rows="2"
                          placeholder="Comentario pedagogico..."></textarea>
            </div>

            <!-- ============ BARRA DESLIZANTE ANIMADA ============ -->
            <div class="placa slider-nota">
                <label class="text-center" style="display:block;margin-bottom:.2rem">Nota (arrastra la barra)</label>
                <div class="slider-valor" id="notaValor">1.0</div>
                <div class="text-center" id="notaEtiqueta" style="font-weight:700">🥀 Plantas marchitas</div>

                <div class="slider-track">
                    <input type="range" class="nota-range" id="notaRange"
                           name="valor" min="1" max="5" step="0.1" value="3.5"
                           data-inicial="3.5">
                    <div class="progreso-nota"><div class="relleno" id="notaProgreso"></div></div>
                    <div class="escala-nota"><span>1.0</span><span>2.0</span><span>3.0</span><span>4.0</span><span>5.0</span></div>
                </div>

                <div class="flex gap center" style="justify-content:center">
                    <button type="button" class="btn btn-sm" data-nota-paso="-0.5">− 0.5</button>
                    <button type="button" class="btn btn-sm" data-nota-paso="-0.1">− 0.1</button>
                    <button type="button" class="btn btn-sm" data-nota-paso="0.1">+ 0.1</button>
                    <button type="button" class="btn btn-sm" data-nota-paso="0.5">+ 0.5</button>
                </div>
                <input type="hidden" id="valorNota" value="3.5">
            </div>

            <button class="btn btn-verde btn-block mt">💾 Guardar nota y notificar</button>
        </form>
    </div>

    <div class="card">
        <h3>🎼 Ultimas notas que registraste</h3>
        <?php if (!$ultimas): ?>
            <p class="text-dim">Aun no has registrado notas.</p>
        <?php else: ?>
            <table class="tabla tabla-mini mt">
                <thead><tr><th>Estudiante</th><th>Materia</th><th>Periodo</th><th>Nota</th></tr></thead>
                <tbody>
                <?php foreach ($ultimas as $n): ?>
                    <tr>
                        <td><?= e($n['nombre'] . ' ' . $n['apellido']) ?></td>
                        <td><?= e($n['materia']) ?></td>
                        <td class="text-dim"><?= e($n['periodo']) ?></td>
                        <td><strong style="color:<?= (float) $n['valor'] >= 4 ? 'var(--green-300)' : ((float) $n['valor'] >= 3 ? 'var(--rock-gold)' : '#ef5350') ?>">
                            <?= number_format((float) $n['valor'], 1) ?>
                        </strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="<?= asset('js/slider.js') ?>"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
