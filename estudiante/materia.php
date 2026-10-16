<?php
/**
 * estudiante/materia.php
 * ---------------------------------------------------------------------
 * El JARDIN VIVO de una materia. La nota se representa como un jardin:
 *   - Nota alta  -> flores, frutos, aves e insectos polinizadores.
 *   - Nota media -> crecimiento moderado.
 *   - Nota baja  -> plantas marchitas y plagas.
 * Ademas: numero de nota visible y musica rock segun el rendimiento.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

use App\Models\Materia;
use App\Support\Jardin;

/** @var \App\Models\Estudiante $usuario */
$materiaId = (int) ($_GET['id'] ?? 0);
$materia = (new Materia())->find($materiaId);
if ($materia === null) {
    header('Location: ' . url('estudiante/dashboard.php'));
    exit;
}

$historial = $usuario->historialMateria($materiaId);
$ultima = $historial ? (float) $historial[count($historial) - 1]['valor'] : null;
$promedio = null;
if ($historial) {
    $promedio = round(array_sum(array_map(fn($h) => (float) $h['valor'], $historial)) / count($historial), 1);
}
$jardin = Jardin::paraNota($ultima);

$pageTitle = $materia['nombre'];
$activo = url('estudiante/dashboard.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion">
    <h2><?= $materia['icono'] ?> <?= e($materia['nombre']) ?></h2>
    <a class="btn btn-sm btn-ghost" href="<?= url('estudiante/dashboard.php') ?>">← Volver a mis materias</a>
</div>

<div class="jardin-panel">
    <!-- ============ EL JARDIN (canvas) ============ -->
    <div>
        <div class="jardin-marco">
            <canvas id="jardin" width="900" height="520"
                    data-estado="<?= e($jardin['estado']) ?>"
                    data-factor="<?= e((string) $jardin['factor']) ?>"
                    data-flores="<?= (int) $jardin['flores'] ?>"
                    data-frutos="<?= (int) $jardin['frutos'] ?>"
                    data-aves="<?= (int) $jardin['aves'] ?>"
                    data-insectos="<?= (int) $jardin['insectos'] ?>"
                    data-plagas="<?= (int) $jardin['plagas'] ?>"></canvas>
        </div>
        <p class="text-dim text-center mt"><?= $jardin['emoji'] ?> <?= e($jardin['descripcion']) ?></p>
    </div>

    <!-- ============ PANEL DE NOTA + MUSICA ============ -->
    <div class="grid">
        <div class="card jardin-info text-center">
            <div class="text-dim" style="text-transform:uppercase;letter-spacing:1px">Ultima nota</div>
            <div class="nota-grande"
                 style="color:<?= e($jardin['color']) ?>">
                <?= $ultima !== null ? number_format($ultima,1) : '—' ?>
            </div>
            <div class="chip <?= $ultima === null ? '' : ($ultima >= 4 ? 'chip-verde' : ($ultima >= 3 ? 'chip-ambar' : 'chip-rojo')) ?>">
                <?= e($jardin['etiqueta']) ?>
            </div>
            <div class="text-dim mt">Promedio de la materia: <strong><?= $promedio !== null ? number_format($promedio,1) : '—' ?></strong></div>

            <div class="jardin-leyenda">
                <div class="fila"><span class="k">🌸</span> Flores: <?= (int) $jardin['flores'] ?></div>
                <div class="fila"><span class="k">🍎</span> Frutos: <?= (int) $jardin['frutos'] ?></div>
                <div class="fila"><span class="k">🐦</span> Aves: <?= (int) $jardin['aves'] ?></div>
                <div class="fila"><span class="k">🐝</span> Polinizadores: <?= (int) $jardin['insectos'] ?></div>
                <?php if ((int) $jardin['plagas'] > 0): ?>
                    <div class="fila"><span class="k">🐛</span> Plagas: <?= (int) $jardin['plagas'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Música rock según rendimiento -->
        <div class="card">
            <h3>🎸 Banda sonora de tu nota</h3>
            <p class="text-dim">Melodia rockera generada segun tu rendimiento.</p>
            <div class="reproductor">
                <button class="play" id="playMusica" title="Reproducir">▶</button>
                <div class="info">
                    <div class="tema" id="temaMusica">—</div>
                    <div class="sub" id="subMusica">Pulsa play</div>
                </div>
                <div class="ecualizador" id="eq" style="visibility:hidden">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>
            <script type="application/json" id="musicaData">
                <?= json_encode(['modo' => $jardin['musica'], 'nota' => $ultima]) ?>
            </script>
        </div>
    </div>
</div>

<!-- ============ HISTORIAL ============ -->
<div class="card mt2">
    <h3>📈 Historial de notas</h3>
    <?php if (!$historial): ?>
        <p class="text-dim">Aun no hay notas registradas en esta materia.</p>
    <?php else: ?>
        <table class="tabla tabla-mini mt">
            <thead><tr><th>Periodo</th><th>Nota</th><th>Observacion</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach (array_reverse($historial) as $h): ?>
                <tr>
                    <td><?= e($h['periodo']) ?></td>
                    <td><strong><?= number_format((float) $h['valor'], 1) ?></strong></td>
                    <td class="text-soft"><?= e($h['observacion'] ?: '—') ?></td>
                    <td class="text-dim"><?= e($h['fecha']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="<?= asset('js/garden.js') ?>"></script>
<script src="<?= asset('js/music.js') ?>"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
