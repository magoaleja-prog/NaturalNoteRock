/* =====================================================================
   main.js - comportamiento global
   - Polling de notificaciones (contador en tiempo real).
   - Avisos flotantes (toasts).
   - Menu movil.
   ===================================================================== */
(function () {
    'use strict';

    const cfg = window.NNR || {};

    /* ---------- Toast ---------- */
    window.mostrarToast = function (texto, tipo) {
        const cont = document.getElementById('toasts');
        if (!cont) return;
        const el = document.createElement('div');
        el.className = 'placa';
        el.style.padding = '.8rem 1rem';
        el.style.minWidth = '240px';
        el.style.borderLeft = '4px solid ' + (tipo === 'error' ? '#c62828' : '#4caf50');
        el.innerHTML = texto;
        cont.appendChild(el);
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(30px)';
            setTimeout(() => el.remove(), 400);
        }, 4200);
    };

    /* ---------- Contador de notificaciones en tiempo real ---------- */
    const contador = document.getElementById('contadorNotif');
    let ultimoTotal = cfg.notifNoLeidas || 0;

    async function refrescarNotificaciones() {
        try {
            const r = await fetch(cfg.apiNotif, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return;
            const data = await r.json();
            if (contador) {
                contador.textContent = data.noLeidas;
                contador.style.display = data.noLeidas > 0 ? 'grid' : 'none';
            }
            // Si llego una nueva, avisamos al vuelo.
            if (data.noLeidas > ultimoTotal) {
                const nueva = data.items.find((n) => !n.leida);
                if (nueva) {
                    window.mostrarToast('🔔 <strong>' + nueva.titulo + '</strong><br>' + nueva.mensaje);
                }
            }
            ultimoTotal = data.noLeidas;
        } catch (e) { /* sin conexion: silencio */ }
    }

    if (cfg.apiNotif) {
        setInterval(refrescarNotificaciones, 8000);
    }

    /* ---------- Menu movil ---------- */
    const toggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('abierto'));
    }
})();
