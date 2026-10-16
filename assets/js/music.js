/* =====================================================================
   music.js - MOTOR DE MUSICA ROCK sintetizada con Web Audio API.
   Se expone como window.RockEngine para reutilizarlo en la pagina de
   musica y en la vista de cada materia. No usa archivos de audio.

   Modos:
     rock_alegre      -> rapido, mayor, muy animado (buenas notas)
     rock_medio       -> groove moderado
     rock_melancolico -> lento, menor, melancolico pero ROCKERO (bajas)
     calm             -> ambiente suave
   ===================================================================== */
(function () {
    'use strict';

    const NOTA_BASE = 110; // A2

    const perfiles = {
        rock_alegre: {
            nombre: '⚡ Riff de la Victoria',
            desc: 'Rock rapido y alegre: ¡tu jardin florece!',
            bpm: 168, escala: [0, 4, 7, 12, 7, 4], dist: 340, onda: 'sawtooth'
        },
        rock_medio: {
            nombre: '🎸 Groove del Crecimiento',
            desc: 'Rock moderado: sigue regando tu jardin.',
            bpm: 120, escala: [0, 3, 7, 10, 7, 3], dist: 240, onda: 'square'
        },
        rock_melancolico: {
            nombre: '🥀 Balada Marchita (Rock)',
            desc: 'Rock lento y melancolico: la planta pide ayuda.',
            bpm: 76, escala: [0, 3, 5, 7, 3, 5], dist: 460, onda: 'sawtooth'
        },
        calm: {
            nombre: '🌱 Semillas en Silencio',
            desc: 'Aun no hay nota: ambiente tranquilo.',
            bpm: 60, escala: [0, 5, 7], dist: 60, onda: 'sine'
        }
    };

    let ctx = null, master = null, dist = null, filtro = null;
    let timer = null, paso = 0, actual = null;
    let onEstado = null;

    function f(tono) { return NOTA_BASE * Math.pow(2, tono / 12); }

    function crearDistorsion(k) {
        const n = 44100, curva = new Float32Array(n);
        for (let i = 0; i < n; i++) {
            const x = (i * 2) / n - 1;
            curva[i] = ((3 + k) * x * 20 * (Math.PI / 180)) / (Math.PI + k * Math.abs(x));
        }
        dist.curve = curva;
        dist.oversample = '4x';
    }

    function init() {
        ctx = new (window.AudioContext || window.webkitAudioContext)();
        master = ctx.createGain();
        master.gain.value = 0;
        dist = ctx.createWaveShaper();
        filtro = ctx.createBiquadFilter();
        filtro.type = 'lowpass';
        filtro.frequency.value = 2600;
        filtro.Q.value = 1.2;
        dist.connect(filtro);
        filtro.connect(master);
        master.connect(ctx.destination);
    }

    function nota(tono, cuando, dur, vol, onda) {
        const osc = ctx.createOscillator();
        const g = ctx.createGain();
        osc.type = onda || actual.onda;
        osc.frequency.value = f(tono);
        g.gain.setValueAtTime(0.0001, cuando);
        g.gain.exponentialRampToValueAtTime(vol, cuando + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, cuando + dur);
        osc.connect(g); g.connect(dist);
        osc.start(cuando); osc.stop(cuando + dur + 0.05);
    }

    function bateria(cuando, tipo) {
        if (tipo === 'bombo') {
            const osc = ctx.createOscillator();
            const g = ctx.createGain();
            osc.frequency.setValueAtTime(150, cuando);
            osc.frequency.exponentialRampToValueAtTime(45, cuando + 0.16);
            g.gain.setValueAtTime(0.9, cuando);
            g.gain.exponentialRampToValueAtTime(0.001, cuando + 0.22);
            osc.connect(g); g.connect(master);
            osc.start(cuando); osc.stop(cuando + 0.24);
        } else {
            const buf = ctx.createBuffer(1, 2048, ctx.sampleRate);
            const d = buf.getChannelData(0);
            for (let i = 0; i < 2048; i++) d[i] = Math.random() * 2 - 1;
            const src = ctx.createBufferSource();
            const g = ctx.createGain();
            const hp = ctx.createBiquadFilter();
            hp.type = 'highpass';
            hp.frequency.value = tipo === 'caja' ? 1200 : 6000;
            src.buffer = buf;
            g.gain.setValueAtTime(tipo === 'caja' ? 0.4 : 0.12, cuando);
            g.gain.exponentialRampToValueAtTime(0.001, cuando + (tipo === 'caja' ? 0.18 : 0.06));
            src.connect(hp); hp.connect(g); g.connect(master);
            src.start(cuando);
        }
    }

    function programar() {
        const beat = 60 / actual.bpm / 2;
        const ahora = ctx.currentTime + 0.06;
        const tono = actual.escala[paso % actual.escala.length];
        nota(tono, ahora, beat * 0.9, 0.26);
        nota(tono + 7, ahora, beat * 0.9, 0.16);
        nota(tono - 12, ahora, beat * 0.9, 0.18);
        if (paso % 2 === 0) bateria(ahora, 'bombo');
        bateria(ahora, paso % 4 === 2 ? 'caja' : 'charles');
        paso++;
    }

    const RockEngine = {
        perfiles,
        get modo() { return actual; },
        get sonando() { return timer !== null; },
        onEstado: (fn) => { onEstado = fn; },

        /** Reproduce un modo; si ya sonaba, lo cambia. */
        play(clave) {
            const perfil = perfiles[clave] || perfiles.rock_medio;
            if (!ctx) init();
            if (ctx.state === 'suspended') ctx.resume();

            if (timer) { clearInterval(timer); timer = null; }

            actual = perfil;
            crearDistorsion(perfil.dist);
            master.gain.cancelScheduledValues(ctx.currentTime);
            master.gain.setValueAtTime(0.0001, ctx.currentTime);
            master.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 1.0);
            const beat = 60 / perfil.bpm / 2;
            timer = setInterval(programar, beat * 1000);
            if (onEstado) onEstado(true, perfil);
        },

        stop() {
            if (timer) { clearInterval(timer); timer = null; }
            if (master) master.gain.linearRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
            if (onEstado) onEstado(false, actual);
        },

        toggle(clave) {
            if (timer && actual && actual === (perfiles[clave] || perfiles.rock_medio)) {
                this.stop();
            } else {
                this.play(clave);
            }
        }
    };

    window.RockEngine = RockEngine;

    /* ---- Auto-vinculacion en la vista de materia ---- */
    const btn = document.getElementById('playMusica');
    const dataEl = document.getElementById('musicaData');
    if (btn && dataEl) {
        const data = JSON.parse(dataEl.textContent || '{}');
        const modo = data.modo || 'rock_medio';
        const perfil = perfiles[modo] || perfiles.rock_medio;
        document.getElementById('temaMusica').textContent = perfil.nombre;
        document.getElementById('subMusica').textContent = perfil.desc;
        const eq = document.getElementById('eq');

        RockEngine.onEstado((sonando) => {
            btn.textContent = sonando ? '⏸' : '▶';
            if (eq) eq.style.visibility = sonando ? 'visible' : 'hidden';
        });
        btn.addEventListener('click', () => RockEngine.toggle(modo));
    }
})();
