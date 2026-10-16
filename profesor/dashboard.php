<?php
/**
 * profesor/dashboard.php
 * ---------------------------------------------------------------------
 * Panel docente: resumen de la gestion. IMPORTANTE: el profesor NO
 * tiene acceso al jardin visual del estudiante (eso es privado).
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['profesor'];
require_once __DIR__ . '/../includes/guard.php';

use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Inasistencia;

/** @var \App\Models\Profesor $usuario */
$nota = new Nota();
$inasist = new Inasistencia();
$estudiantes = $usuario->listarEstudiantes();
$ultimas = $nota->ultimas(8);
$inasRecientes = $inasist->recientes(6);

$pageTitle = 'Panel docente';
$activo = url('profesor/dashboard.php');
require __DIR__ . '/../includes/header.php';
?>

<section class="card mb">
    <h2>¡Bienvenido, profe <?= e($usuario->getNombre()) ?>! 🎸</h2>
    <p class="text-soft">
        <?= e($usuario->getEspecialidad()) ?> · <?= e($usuario->getTitulo()) ?>
    </p>
    <div class="grid grid-4 mt">
        <div class="stat"><div class="valor"><?= count($estudiantes) ?></div><div class="etiqueta">Estudiantes</div></div>
        <div class="stat"><div class="valor"><?= $nota->contar() ?></div><div class="etiqueta">Notas</div></div>
        <div class="stat"><div class="valor"><?= $inasist->contar() ?></div><div class="etiqueta">Inasistencias</div></div>
        <div class="stat"><div class="valor"><?= $usuario->notificacionesNoLeidas() ?></div><div class="etiqueta">Avisos</div></div>
    </div>
    <div class="flex gap mt">
        <a class="btn btn-verde" href="<?= url('profesor/notas.php') ?>">🎚️ Registrar nota</a>
        <a class="btn btn-ambar" href="<?= url('profesor/inasistencias.php') ?>">📋 Registrar inasistencia</a>
    </div>
</section>

<div class="grid grid-2">
    <div class="card">
        <h3>🎼 Ultimas notas registradas</h3>
        <?php if (!$ultimas): ?>
            <p class="text-dim">Aun no hay notas.</p>
        <?php else: ?>
            <table class="tabla tabla-mini mt">
                <thead><tr><th>Estudiante</th><th>Materia</th><th>Nota</th></tr></thead>
                <tbody>
                <?php foreach ($ultimas as $n): ?>
                    <tr>
                        <td><?= e($n['nombre'] . ' ' . $n['apellido']) ?></td>
                        <td><?= e($n['materia']) ?></td>
                        <td><strong style="color:<?= (float) $n['valor'] >= 4 ? 'var(--green-300)' : ((float) $n['valor'] >= 3 ? 'var(--rock-gold)' : '#ef5350') ?>">
                            <?= number_format((float) $n['valor'], 1) ?>
                        </strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>📋 Inasistencias recientes</h3>
        <?php if (!$inasRecientes): ?>
            <p class="text-dim">Sin inasistencias registradas.</p>
        <?php else: ?>
            <table class="tabla tabla-mini mt">
                <thead><tr><th>Estudiante</th><th>Materia</th><th>Tipo</th><th>Fecha</th></tr></thead>
                <tbody>
                <?php foreach ($inasRecientes as $i): ?>
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

<div class="alerta alerta-info mt2">
    🔒 Recuerda: el <strong>jardín visual</strong> es privado del estudiante.
    Aquí solo gestionas notas e inasistencias.
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
