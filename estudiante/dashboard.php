<?php
/**
 * estudiante/dashboard.php
 * ---------------------------------------------------------------------
 * Panel del estudiante: su foto, sus estadisticas y una PLACA METALICA
 * por cada materia (12), con la ultima nota y el estado del jardin.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

use App\Support\Jardin;

/** @var \App\Models\Estudiante $usuario */
$materias = $usuario->materiasConNotas();
$promedio = $usuario->promedioGeneral();
$inasist  = $usuario->totalInasistencias();
$jardinG  = Jardin::paraNota($promedio);

$pageTitle = 'Mi jardin';
$activo = url('estudiante/dashboard.php');
require __DIR__ . '/../includes/header.php';
?>

<!-- ============ CABECERA DE BIENVENIDA CON FOTO ============ -->
<section class="card flex gap wrap center mb">
    <img src="<?= e($usuario->getFotoUrl()) ?>" alt="Mi foto"
         style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:4px solid var(--green-500);box-shadow:0 8px 24px rgba(0,0,0,.6)">
    <div style="flex:1;min-width:220px">
        <h2>¡Hola, <?= e($usuario->getNombre()) ?>! 🎸🌿</h2>
        <p class="text-soft">
            Curso <?= e($usuario->getCurso()) ?> · Código <?= e($usuario->getCodigo()) ?>
        </p>
        <p class="text-dim">
            Tu jardin general esta
            <strong style="color:<?= e($jardinG['color']) ?>"><?= e($jardinG['etiqueta']) ?></strong>
            <?= $jardinG['emoji'] ?>
        </p>
        <a class="btn btn-sm btn-ambar" href="<?= url('estudiante/perfil.php') ?>">📸 Editar mi perfil</a>
    </div>
    <div class="grid grid-2" style="min-width:230px">
        <div class="stat"><div class="valor"><?= $promedio !== null ? number_format($promedio,1) : '—' ?></div><div class="etiqueta">Promedio</div></div>
        <div class="stat"><div class="valor"><?= count($materias) ?></div><div class="etiqueta">Materias</div></div>
        <div class="stat"><div class="valor"><?= $inasist ?></div><div class="etiqueta">Inasistencias</div></div>
        <div class="stat"><div class="valor"><?= $usuario->notificacionesNoLeidas() ?></div><div class="etiqueta">Avisos</div></div>
    </div>
</section>

<div class="titulo-seccion">
    <h2>🎛️ Mis materias</h2>
</div>

<!-- ============ PLACAS METALICAS POR MATERIA ============ -->
<div class="grid grid-placas">
    <?php foreach ($materias as $m):
        $nota = $m['valor'] !== null ? (float) $m['valor'] : null;
        $j = Jardin::paraNota($nota);
    ?>
        <a class="placa materia" style="--mat: <?= e($m['color']) ?>"
           href="<?= url('estudiante/materia.php?id=' . (int) $m['id']) ?>">
            <div class="flex between center">
                <span class="placa-icono"><?= $m['icono'] ?></span>
                <span class="placa-nota"><?= $nota !== null ? number_format($nota,1) : '—' ?></span>
            </div>
            <div style="margin-top:.6rem;font-family:'Oswald',sans-serif;font-weight:600;font-size:1.05rem">
                <?= e($m['nombre']) ?>
            </div>
            <div class="flex between center" style="margin-top:.5rem">
                <span class="badge-jardin"><?= $j['emoji'] ?> <?= e($j['etiqueta']) ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
