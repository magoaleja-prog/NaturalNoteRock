<?php
/**
 * estudiante/musica.php
 * ---------------------------------------------------------------------
 * Reproductor de musica rock con "listas" (modos de sintesis).
 * La musica se genera en vivo con Web Audio API segun el estado de
 * animo; se puede elegir pista manualmente.
 * ---------------------------------------------------------------------
 */

declare(strict_types=1);

$rolesPermitidos = ['estudiante'];
require_once __DIR__ . '/../includes/guard.php';

use App\Support\Jardin;

/** @var \App\Models\Estudiante $usuario */
$promedio = $usuario->promedioGeneral();
$sugerido = Jardin::paraNota($promedio)['musica'];

$pageTitle = 'Musica rock';
$activo = url('estudiante/musica.php');
require __DIR__ . '/../includes/header.php';
?>

<div class="titulo-seccion"><h2>🎧 Radio Natural Note Rock</h2></div>
<p class="text-soft mb">
    Tu banda sonora se elige según tu rendimiento (promedio actual:
    <strong><?= $promedio !== null ? number_format($promedio,1) : '—' ?></strong>).
    También puedes cambiar de pista.
</p>

<div class="grid grid-2">
    <div class="card">
        <div class="reproductor">
            <button class="play" id="btnPlay">▶</button>
            <div class="info">
                <div class="tema" id="temaActual">Pulsa play 🎸</div>
                <div class="sub" id="subActual">Selecciona una pista de la lista</div>
            </div>
            <div class="ecualizador" id="eq" style="visibility:hidden">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <p class="hint mt">🎵 Sugerido para tu nota: <strong id="sugerido"></strong></p>
    </div>

    <div class="card">
        <h3>Lista de pistas</h3>
        <div class="lista-temas mt" id="listaTemas"></div>
    </div>
</div>

<div class="card mt2">
    <h3>🥁 ¿Como suena tu rendimiento?</h3>
    <ul class="text-soft" style="margin-left:1.2rem">
        <li><strong>Nota alta</strong>: rock rápido, mayor y muy animado.</li>
        <li><strong>Nota media</strong>: groove roquero moderado.</li>
        <li><strong>Nota baja</strong>: balada lenta en tono menor, melancólica pero <em>rockera</em>.</li>
        <li><strong>Sin notas</strong>: ambiente suave de "semillas en silencio".</li>
    </ul>
</div>

<script src="<?= asset('js/music.js') ?>"></script>
<script>
    // Pistas disponibles (modos del motor RockEngine)
    const pistas = [
        { clave: 'rock_alegre',      emoji: '⚡' },
        { clave: 'rock_medio',       emoji: '🎸' },
        { clave: 'rock_melancolico', emoji: '🥀' },
        { clave: 'calm',             emoji: '🌱' }
    ];
    const sugerido = <?= json_encode($sugerido) ?>;
    const lista = document.getElementById('listaTemas');
    const temaActual = document.getElementById('temaActual');
    const subActual = document.getElementById('subActual');
    const btnPlay = document.getElementById('btnPlay');
    const eq = document.getElementById('eq');
    const sugeridoEl = document.getElementById('sugerido');

    const sugeridoPerfil = RockEngine.perfiles[sugerido] || RockEngine.perfiles.rock_medio;
    sugeridoEl.textContent = sugeridoPerfil.nombre;

    let seleccion = sugerido;

    pistas.forEach((p) => {
        const perfil = RockEngine.perfiles[p.clave];
        const el = document.createElement('div');
        el.className = 'tema-item';
        el.dataset.clave = p.clave;
        el.innerHTML = '<span style="font-size:1.3rem">' + p.emoji + '</span>' +
            '<div style="flex:1"><strong>' + perfil.nombre + '</strong>' +
            '<div class="text-dim" style="font-size:.8rem">' + perfil.desc + '</div></div>' +
            (p.clave === sugerido ? '<span class="chip chip-verde">Sugerida</span>' : '');
        el.addEventListener('click', () => {
            seleccion = p.clave;
            marcarActivo();
            RockEngine.play(p.clave);
        });
        lista.appendChild(el);
    });

    function marcarActivo() {
        document.querySelectorAll('.tema-item').forEach((e) => {
            e.classList.toggle('activo', e.dataset.clave === seleccion);
        });
    }

    RockEngine.onEstado((sonando, perfil) => {
        btnPlay.textContent = sonando ? '⏸' : '▶';
        eq.style.visibility = sonando ? 'visible' : 'hidden';
        if (perfil) {
            temaActual.textContent = perfil.nombre;
            subActual.textContent = perfil.desc;
        }
    });

    btnPlay.addEventListener('click', () => {
        if (RockEngine.sonando) { RockEngine.stop(); }
        else { RockEngine.play(seleccion); }
    });
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
