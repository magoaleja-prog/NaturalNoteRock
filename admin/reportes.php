<?php
/**
 * admin/reportes.php
 * ---------------------------------------------------------------------
 * Reportes institucionales: promedio por materia, estudiantes en riesgo
 * (promedio < 3.0) y ranking de inasistencias.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['administrador'];
require_once __DIR__ . '/../includes/guard.php';

use App\Core\Database;

/** @var \App\Models\Administrador $usuario */
$db = Database::getInstancia()->getConexion();

$porMateria = $db->query(
    'SELECT m.nombre, m.icono, m.color, COUNT(n.id) AS total,
            ROUND(AVG(n.valor),2) AS promedio, MIN(n.valor) AS minimo, MAX(n.valor) AS maximo
       FROM materias m
       LEFT JOIN notas n ON n.materia_id = m.id
      GROUP BY m.id
      ORDER BY promedio DESC'
)->fetchAll();

$enRiesgo = $db->query(
    'SELECT u.nombre, u.apellido, e.curso, ROUND(AVG(n.valor),1) AS promedio,
            COUNT(n.id) AS materias
       FROM estudiantes e
       JOIN usuarios u ON u.id = e.usuario_id
       JOIN notas n ON n.estudiante_id = e.id
      GROUP BY e.id
     HAVING promedio < 3.0
      ORDER BY promedio ASC'
)->fetchAll();

$inasistencias = $db->query(
    'SELECT u.nombre, u.apellido, e.curso,
            COUNT(i.id) AS total,
            SUM(i.justificada) AS justificadas
       FROM estudiantes e
       JOIN usuarios u ON u.id = e.usuario_id
       LEFT JOIN inasistencias i ON i.estudiante_id = e.id
      GROUP BY e.id
      ORDER BY total DESC'
)->fetchAll();

$pageTitle = 'Reportes';
$activo = url('admin/reportes.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>📈 Reportes institucionales</h2></div>

<!-- ============ PROMEDIO POR MATERIA ============ -->
<div class="card mb">
    <h3>🎼 Promedio por materia</h3>
    <table class="tabla tabla-mini mt">
        <thead><tr><th>Materia</th><th>Notas</th><th>Promedio</th><th>Min</th><th>Max</th><th>Tendencia</th></tr></thead>
        <tbody>
        <?php foreach ($porMateria as $m): $p = $m['promedio'] !== null ? (float) $m['promedio'] : null; ?>
            <tr>
                <td><?= $m['icono'] ?> <?= e($m['nombre']) ?></td>
                <td><?= (int) $m['total'] ?></td>
                <td><strong><?= $p !== null ? number_format($p,2) : '—' ?></strong></td>
                <td class="text-dim"><?= $m['minimo'] !== null ? number_format((float) $m['minimo'],1) : '—' ?></td>
                <td class="text-dim"><?= $m['maximo'] !== null ? number_format((float) $m['maximo'],1) : '—' ?></td>
                <td>
                    <div class="progreso-nota" style="width:140px">
                        <div class="relleno" style="width:<?= $p !== null ? (($p - 1) / 4) * 100 : 0 ?>%;background:<?= $p !== null && $p >= 4 ? '#43a047' : ($p !== null && $p >= 3 ? '#f9a825' : '#ef5350') ?>"></div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="grid grid-2">
    <!-- ============ ESTUDIANTES EN RIESGO ============ -->
    <div class="card">
        <h3>⚠️ Estudiantes en riesgo (promedio &lt; 3.0)</h3>
        <?php if (!$enRiesgo): ?>
            <p class="text-dim">¡Excelente! Ningún estudiante está en riesgo. 🌿</p>
        <?php else: ?>
            <table class="tabla tabla-mini mt">
                <thead><tr><th>Estudiante</th><th>Curso</th><th>Promedio</th></tr></thead>
                <tbody>
                <?php foreach ($enRiesgo as $e): ?>
                    <tr>
                        <td><?= e($e['nombre'] . ' ' . $e['apellido']) ?></td>
                        <td class="text-dim"><?= e($e['curso']) ?></td>
                        <td><span class="chip chip-rojo"><?= number_format((float) $e['promedio'],1) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ============ RANKING DE INASISTENCIAS ============ -->
    <div class="card">
        <h3>📋 Ranking de inasistencias</h3>
        <table class="tabla tabla-mini mt">
            <thead><tr><th>Estudiante</th><th>Curso</th><th>Total</th><th>Justificadas</th></tr></thead>
            <tbody>
            <?php foreach ($inasistencias as $i): ?>
                <tr>
                    <td><?= e($i['nombre'] . ' ' . $i['apellido']) ?></td>
                    <td class="text-dim"><?= e($i['curso']) ?></td>
                    <td><?= (int) $i['total'] ?></td>
                    <td class="text-dim"><?= (int) $i['justificadas'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
