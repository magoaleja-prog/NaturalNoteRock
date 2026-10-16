<?php
/**
 * estudiante/aprendizaje.php
 * ---------------------------------------------------------------------
 * Seccion de aprendizaje: lecturas reales y recursos por materia.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

use App\Support\Contenido;

/** @var \App\Models\Estudiante $usuario */
$materias = $usuario->materiasConNotas();

$pageTitle = 'Aprendizaje';
$activo = url('estudiante/aprendizaje.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>📖 Zona de aprendizaje</h2></div>
<p class="text-soft mb">
    Lecturas y recursos reales para reforzar cada materia. ¡Sube tu nota y tu jardín florecerá! 🌿
</p>

<?php foreach ($materias as $m):
    $codigo = $m['codigo'];
    $recursos = Contenido::recursosDe($codigo);
?>
    <div class="card mb">
        <h3 style="color:<?= e($m['color']) ?>">
            <?= $m['icono'] ?> <?= e($m['nombre']) ?>
            <span class="text-dim" style="font-size:.8rem;font-weight:400">
                · <?= e($m['area']) ?>
            </span>
        </h3>
        <p class="text-soft"><?= e($m['descripcion']) ?></p>
        <div class="grid grid-2 mt">
            <?php foreach ($recursos as $r): ?>
                <a class="placa" href="<?= e($r['url']) ?>" target="_blank" rel="noopener">
                    <div class="flex between center">
                        <strong><?= e($r['titulo']) ?></strong>
                        <span class="chip chip-verde"><?= e($r['tipo']) ?></span>
                    </div>
                    <p class="text-soft" style="font-size:.86rem;margin-top:.4rem"><?= e($r['resumen']) ?></p>
                    <span class="text-dim" style="font-size:.75rem">Abrir recurso ↗</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
