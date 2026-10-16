/* =====================================================================
   slider.js - BARRA DESLIZANTE ANIMADA de notas.
   El profesor NO escribe el numero: arrastra una barra y esta avanza
   visualmente. Ademas hay un modo automatico (animarHasta) que llena
   la barra poco a poco hasta el valor final.
   ===================================================================== */
(function () {
    'use strict';

    const range = document.getElementById('notaRange');
    if (!range) return;

    const valorEl   = document.getElementById('notaValor');
    const progreso  = document.getElementById('notaProgreso');
    const etiqueta  = document.getElementById('notaEtiqueta');
    const inputOcult = document.getElementById('valorNota'); // respaldo accesible

    function colorPara(v) {
        if (v >= 4) return '#43a047';
        if (v >= 3) return '#f9a825';
        return '#ef5350';
    }
    function etiquetaPara(v) {
        if (v >= 4) return '🌸 Jardin florecido';
        if (v >= 3) return '🌿 Crecimiento moderado';
        return '🥀 Plantas marchitas';
    }

    function pintar(v, animar) {
        v = Math.round(v * 10) / 10;
        valorEl.textContent = v.toFixed(1);
        valorEl.style.color = colorPara(v);
        const pct = ((v - 1) / 4) * 100; // 1.0 -> 0%, 5.0 -> 100%
        progreso.style.width = pct + '%';
        progreso.style.background = colorPara(v);
        if (etiqueta) {
            etiqueta.textContent = etiquetaPara(v);
            etiqueta.style.color = colorPara(v);
        }
        if (inputOcult) inputOcult.value = v.toFixed(1);
        if (window.NNRGardenPreview && typeof window.NNRGardenPreview === 'function') {
            window.NNRGardenPreview(v);
        }
    }

    // Arrastre manual -> actualiza en vivo.
    range.addEventListener('input', () => pintar(parseFloat(range.value)));

    // Animacion de llenado automatico hasta un objetivo.
    window.animarHasta = function (objetivo, duracion) {
        duracion = duracion || 900;
        const inicio = 1.0;
        const t0 = performance.now();
        function paso(t) {
            const p = Math.min(1, (t - t0) / duracion);
            const ease = 1 - Math.pow(1 - p, 3);
            const v = inicio + (objetivo - inicio) * ease;
            range.value = v.toFixed(1);
            pintar(v);
            if (p < 1) requestAnimationFrame(paso);
        }
        requestAnimationFrame(paso);
    };

    // Estado inicial: la barra "avanza" desde 1.0 hasta el valor por
    // defecto (sugerido por el promedio o 3.5), mostrando la animacion.
    const objetivoInicial = parseFloat(range.dataset.inicial || range.value || '3.5');
    range.value = '1.0';
    pintar(1.0);
    setTimeout(() => window.animarHasta(objetivoInicial, 1100), 250);

    // Botones rapidos (+/- 0.1)
    document.querySelectorAll('[data-nota-paso]').forEach((b) => {
        b.addEventListener('click', () => {
            const paso = parseFloat(b.dataset.notaPaso);
            let v = parseFloat(range.value) + paso;
            v = Math.max(1, Math.min(5, v));
            range.value = v.toFixed(1);
            pintar(v);
        });
    });
})();
