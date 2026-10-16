<?php
/**
 * admin/dashboard.php
 * ---------------------------------------------------------------------
 * Panel general del administrador: metricas globales y auditoria de
 * accesos (tabla Sesiones).
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['administrador'];
require_once __DIR__ . '/../includes/guard.php';

use App\Core\Database;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Nota;
use App\Models\Sesion;

/** @var \App\Models\Administrador $usuario */
$db = Database::getInstancia()->getConexion();
$totalUsuarios = (int) $db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalEstudiantes = (int) $db->query('SELECT COUNT(*) FROM estudiantes')->fetchColumn();
$totalProfesores = (int) $db->query('SELECT COUNT(*) FROM profesores')->fetchColumn();
$sesiones = new Sesion();
$ultimasSesiones = $sesiones->ultimas(10);
$promedioGlobal = $db->query('SELECT ROUND(AVG(valor),1) FROM notas')->fetchColumn();

$pageTitle = 'Panel general';
$activo = url('admin/dashboard.php');
require __DIR__ . '/../includes/header.php';
?>

<section class="card mb">
    <h2>🎛️ Centro de control</h2>
    <p class="text-soft">Gobierno total de <?= e(APP_NAME) ?> · Organización <?= e(APP_ORG) ?></p>
    <div class="grid grid-4 mt">
        <div class="stat"><div class="valor"><?= $totalUsuarios ?></div><div class="etiqueta">Usuarios</div></div>
        <div class="stat"><div class="valor"><?= $totalEstudiantes ?></div><div class="etiqueta">Estudiantes</div></div>
        <div class="stat"><div class="valor"><?= $totalProfesores ?></div><div class="etiqueta">Profesores</div></div>
        <div class="stat"><div class="valor"><?= $promedioGlobal !== null ? number_format((float) $promedioGlobal,1) : '—' ?></div><div class="etiqueta">Promedio global</div></div>
    </div>
    <div class="flex gap mt">
        <a class="btn btn-verde" href="<?= url('admin/usuarios.php') ?>">👥 Gestionar usuarios</a>
        <a class="btn btn-ambar" href="<?= url('admin/materias.php') ?>">📚 Gestionar materias</a>
        <a class="btn" href="<?= url('admin/reportes.php') ?>">📈 Ver reportes</a>
    </div>
</section>

<div class="card">
    <h3>🔐 Auditoria de accesos (Sesiones)</h3>
    <p class="text-dim">Sesiones activas: <strong><?= $sesiones->activas() ?></strong></p>
    <?php if (!$ultimasSesiones): ?>
        <p class="text-dim">Todavia no hay accesos registrados.</p>
    <?php else: ?>
        <table class="tabla tabla-mini mt">
            <thead><tr><th>Usuario</th><th>Rol</th><th>IP</th><th>Inicio</th><th>Estado</th></tr></thead>
            <tbody>
            <?php foreach ($ultimasSesiones as $s): ?>
                <tr>
                    <td><?= e($s['nombre'] . ' ' . $s['apellido']) ?></td>
                    <td><span class="chip"><?= e($s['rol']) ?></span></td>
                    <td class="text-dim"><?= e($s['ip']) ?></td>
                    <td class="text-dim"><?= e($s['creado_en']) ?></td>
                    <td><span class="chip <?= $s['activa'] ? 'chip-verde' : 'chip-rojo' ?>"><?= $s['activa'] ? 'activa' : 'cerrada' ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
