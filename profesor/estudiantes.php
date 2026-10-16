<?php
/**
 * profesor/estudiantes.php
 * ---------------------------------------------------------------------
 * Listado de estudiantes con su promedio y total de inasistencias.
 * El profesor ve DATOS, no el jardin visual del estudiante.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['profesor'];
require_once __DIR__ . '/../includes/guard.php';

use App\Core\Database;

/** @var \App\Models\Profesor $usuario */
$db = Database::getInstancia()->getConexion();
$sql = 'SELECT e.id, e.codigo, e.curso, u.nombre, u.apellido, u.foto, u.email,
               ROUND(AVG(n.valor),1) AS promedio,
               (SELECT COUNT(*) FROM inasistencias i WHERE i.estudiante_id = e.id) AS inasistencias
          FROM estudiantes e
          JOIN usuarios u ON u.id = e.usuario_id
          LEFT JOIN notas n ON n.estudiante_id = e.id
         GROUP BY e.id
         ORDER BY e.curso, u.apellido';
$estudiantes = $db->query($sql)->fetchAll();

$pageTitle = 'Estudiantes';
$activo = url('profesor/estudiantes.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>🧑‍🎓 Mis estudiantes</h2>
    <span class="chip chip-verde"><?= count($estudiantes) ?> en total</span>
</div>

<div class="card">
    <table class="tabla">
        <thead>
            <tr><th>Foto</th><th>Estudiante</th><th>Curso</th><th>Codigo</th><th>Promedio</th><th>Inasistencias</th></tr>
        </thead>
        <tbody>
        <?php foreach ($estudiantes as $e):
            $prom = $e['promedio'] !== null ? (float) $e['promedio'] : null;
        ?>
            <tr>
                <td>
                    <?php
                    $foto = $e['foto'] && is_file(APP_ROOT . '/' . $e['foto'])
                        ? url($e['foto']) : asset('img/avatar_default.svg');
                    ?>
                    <img src="<?= e($foto) ?>" alt=""
                         style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid var(--green-500)">
                </td>
                <td>
                    <strong><?= e($e['nombre'] . ' ' . $e['apellido']) ?></strong>
                    <div class="text-dim" style="font-size:.78rem"><?= e($e['email']) ?></div>
                </td>
                <td><?= e($e['curso']) ?></td>
                <td class="text-dim"><?= e($e['codigo']) ?></td>
                <td>
                    <span class="chip <?= $prom === null ? '' : ($prom >= 4 ? 'chip-verde' : ($prom >= 3 ? 'chip-ambar' : 'chip-rojo')) ?>">
                        <?= $prom !== null ? number_format($prom, 1) : '—' ?>
                    </span>
                </td>
                <td><?= (int) $e['inasistencias'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
