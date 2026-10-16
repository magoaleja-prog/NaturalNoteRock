/* =====================================================================
   games.js - Juegos interactivos educativos
   1. Trivia Rockera
   2. Memorama Natural
   ===================================================================== */
(function () {
    'use strict';

    /* ---------------- TRIVIA ROCKERA ---------------- */
    const trivia = window.TRIVIA || [];
    const app = document.getElementById('triviaApp');
    if (app && trivia.length) {
        const elPreg = document.getElementById('triviaPregunta');
        const elOpc  = document.getElementById('triviaOpciones');
        const elPunt = document.getElementById('triviaPuntaje');
        const elProg = document.getElementById('triviaProgreso');
        const elRein = document.getElementById('triviaReiniciar');
        let idx = 0, puntaje = 0, bloqueado = false;

        function pintar() {
            bloqueado = false;
            const q = trivia[idx];
            elProg.textContent = 'Pregunta ' + (idx + 1) + ' de ' + trivia.length +
                ' · ' + q.materia;
            elPreg.textContent = q.p;
            elOpc.innerHTML = '';
            q.opciones.forEach((op, i) => {
                const b = document.createElement('button');
                b.className = 'btn opcion';
                b.textContent = op;
                b.addEventListener('click', () => responder(i, b));
                elOpc.appendChild(b);
            });
        }

        function responder(i, boton) {
            if (bloqueado) return;
            bloqueado = true;
            const q = trivia[idx];
            const correcto = i === q.correcta;
            if (correcto) {
                puntaje += 10;
                elPunt.textContent = puntaje;
                boton.classList.add('btn-verde');
                window.mostrarToast('✅ ¡Correcto! +10');
            } else {
                boton.classList.add('btn-rojo');
                elOpc.children[q.correcta].classList.add('btn-verde');
                window.mostrarToast('❌ Era: ' + q.opciones[q.correcta], 'error');
            }
            setTimeout(() => {
                idx++;
                if (idx < trivia.length) { pintar(); }
                else {
                    elPreg.textContent = '🎉 ¡Terminaste! Puntaje final: ' + puntaje + ' pts';
                    elOpc.innerHTML = '';
                    elRein.style.display = 'inline-flex';
                }
            }, 1100);
        }

        elRein.addEventListener('click', () => {
            idx = 0; puntaje = 0; elPunt.textContent = '0';
            elRein.style.display = 'none';
            pintar();
        });

        pintar();
    }

    /* ---------------- MEMORAMA NATURAL ---------------- */
    const cont = document.getElementById('memorama');
    if (cont) {
        const iconos = ['🌻', '🎸', '🐝', '🍎', '🌵', '🦋', '🥁', '🍄'];
        const intentosEl = document.getElementById('memIntentos');
        const parejasEl = document.getElementById('memParejas');
        let primera = null, segunda = null, bloquear = false;
        let intentos = 0, parejas = 0;

        function barajar(a) {
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        function iniciar() {
            cont.innerHTML = '';
            primera = segunda = null; bloquear = false;
            intentos = 0; parejas = 0;
            intentosEl.textContent = '0';
            parejasEl.textContent = '0';
            const cartas = barajar([...iconos, ...iconos]);
            cartas.forEach((ico) => {
                const c = document.createElement('button');
                c.className = 'placa';
                c.style.cssText = 'height:74px;font-size:1.8rem;cursor:pointer;display:grid;place-items:center;padding:0';
                c.dataset.icono = ico;
                c.textContent = '❓';
                c.addEventListener('click', () => voltear(c));
                cont.appendChild(c);
            });
        }

        function voltear(c) {
            if (bloquear || c.classList.contains('volteada') || c === primera) return;
            c.textContent = c.dataset.icono;
            c.classList.add('volteada');

            if (!primera) { primera = c; return; }
            segunda = c;
            intentos++;
            intentosEl.textContent = intentos;

            if (primera.dataset.icono === segunda.dataset.icono) {
                parejas++;
                parejasEl.textContent = parejas;
                primera = segunda = null;
                if (parejas === iconos.length) {
                    window.mostrarToast('🏆 ¡Completaste el memorama en ' + intentos + ' intentos!');
                }
            } else {
                bloquear = true;
                setTimeout(() => {
                    primera.textContent = '❓';
                    segunda.textContent = '❓';
                    primera.classList.remove('volteada');
                    segunda.classList.remove('volteada');
                    primera = segunda = null;
                    bloquear = false;
                }, 800);
            }
        }

        document.getElementById('memReiniciar').addEventListener('click', iniciar);
        iniciar();
    }
})();
