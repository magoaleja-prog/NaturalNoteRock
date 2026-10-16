<?php
/**
 * estudiante/juegos.php
 * ---------------------------------------------------------------------
 * Juegos interactivos educativos:
 *   1. Trivia Rockera (preguntas de todas las materias).
 *   2. Memorama Natural (memoria con iconos de naturaleza y rock).
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

use App\Support\Contenido;

/** @var \App\Models\Estudiante $usuario */
$trivia = Contenido::trivia();
shuffle($trivia);
$trivia = array_slice($trivia, 0, 8);

$pageTitle = 'Juegos';
$activo = url('estudiante/juegos.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>🎮 Espacios de juego</h2></div>
<p class="text-soft mb">Aprende jugando. ¡Sube tu puntaje y demuestra tu estilo rockero! 🤘</p>

<div class="grid grid-2">
    <!-- ============ TRIVIA ROCKERA ============ -->
    <div class="card juego-caja" id="triviaApp">
        <h3>⚡ Trivia Rockera</h3>
        <p class="text-dim" id="triviaProgreso">Pregunta 1 de <?= count($trivia) ?></p>
        <div class="placa" style="--mat:#f9a825;margin:1rem 0">
            <p id="triviaPregunta" style="font-size:1.05rem;font-weight:700">Cargando…</p>
        </div>
        <div class="opciones" id="triviaOpciones"></div>
        <p class="mt"><strong>Puntaje: <span id="triviaPuntaje">0</span></strong></p>
        <button class="btn btn-verde btn-sm mt" id="triviaReiniciar" style="display:none">🔁 Jugar de nuevo</button>
    </div>

    <!-- ============ MEMORAMA ============ -->
    <div class="card juego-caja">
        <h3>🧠 Memorama Natural</h3>
        <p class="text-dim">Encuentra las parejas de naturaleza y rock.</p>
        <div id="memorama" style="display:grid;grid-template-columns:repeat(4,1fr);gap:.6rem;margin-top:1rem"></div>
        <p class="mt"><strong>Intentos: <span id="memIntentos">0</span></strong> ·
            <strong>Parejas: <span id="memParejas">0</span></strong></p>
        <button class="btn btn-ambar btn-sm mt" id="memReiniciar">🔁 Reiniciar</button>
    </div>
</div>

<script>
    window.TRIVIA = <?= json_encode($trivia, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= asset('js/games.js') ?>"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
