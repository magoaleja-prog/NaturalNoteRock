/* =====================================================================
   garden.js - JARDIN VIVO dibujado en <canvas> con animacion.
   La apariencia depende del ESTADO de la nota:
     alto  -> florido, frutos, aves, polinizadores
     medio -> crecimiento moderado
     bajo  -> plantas marchitas y plagas
   ===================================================================== */
(function () {
    'use strict';

    const canvas = document.getElementById('jardin');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const W = canvas.width;
    const H = canvas.height;
    const estado = canvas.dataset.estado || 'medio';
    const factor = parseFloat(canvas.dataset.factor || '0.5');
    const nFlores   = parseInt(canvas.dataset.flores || '0', 10);
    const nFrutos   = parseInt(canvas.dataset.frutos || '0', 10);
    const nAves     = parseInt(canvas.dataset.aves || '0', 10);
    const nInsectos = parseInt(canvas.dataset.insectos || '0', 10);
    const nPlagas   = parseInt(canvas.dataset.plagas || '0', 10);

    const paleta = {
        alto:  { cielo: ['#0b3d1e', '#2e7d32', '#8bc34a'], suelo: '#3e2723', planta: '#43a047' },
        medio: { cielo: ['#1a2a1a', '#4e6b2f', '#c0ca33'], suelo: '#4e342e', planta: '#9e9d24' },
        bajo:  { cielo: ['#2a1f14', '#5d4a2a', '#8d6e63'], suelo: '#3e2723', planta: '#8d6e63' },
        sin_nota: { cielo: ['#10131a', '#26312a', '#4a5d43'], suelo: '#3e2723', planta: '#7cb342' }
    }[estado] || { cielo: ['#10131a', '#26312a', '#4a5d43'], suelo: '#3e2723', planta: '#7cb342' };

    // --- Entidades animadas ---
    const plantas = [];
    const totalPlantas = Math.max(4, Math.round(4 + factor * 12));
    for (let i = 0; i < totalPlantas; i++) {
        plantas.push({
            x: (i + 0.5) * (W / totalPlantas) + (Math.random() * 20 - 10),
            base: H - 70 + Math.random() * 20,
            alto: 60 + factor * 190 + Math.random() * 30,
            fase: Math.random() * Math.PI * 2,
            marchita: estado === 'bajo' && Math.random() < 0.7,
            semilla: estado === 'sin_nota'
        });
    }

    const particulas = [];
    function crearVolador(tipo) {
        return {
            tipo,
            x: Math.random() * W,
            y: 60 + Math.random() * 160,
            vx: (Math.random() * 0.9 + 0.4) * (Math.random() < 0.5 ? -1 : 1),
            vy: 0,
            fase: Math.random() * Math.PI * 2,
            escala: 0.7 + Math.random() * 0.7
        };
    }
    for (let i = 0; i < nAves; i++) particulas.push(crearVolador('ave'));
    for (let i = 0; i < nInsectos; i++) particulas.push(crearVolador('abeja'));
    for (let i = 0; i < nPlagas; i++) particulas.push(crearVolador('plaga'));

    let t = 0;

    function fondo() {
        const g = ctx.createLinearGradient(0, 0, 0, H);
        g.addColorStop(0, paleta.cielo[0]);
        g.addColorStop(0.55, paleta.cielo[1]);
        g.addColorStop(1, paleta.cielo[2]);
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, W, H);

        // Sol (mas brillante en notas altas)
        ctx.save();
        ctx.globalAlpha = 0.25 + factor * 0.6;
        const sol = ctx.createRadialGradient(W - 130, 100, 10, W - 130, 100, 110);
        sol.addColorStop(0, '#fff59d');
        sol.addColorStop(1, 'rgba(255,241,118,0)');
        ctx.fillStyle = sol;
        ctx.beginPath(); ctx.arc(W - 130, 100, 110, 0, Math.PI * 2); ctx.fill();
        ctx.restore();

        // Suelo
        ctx.fillStyle = paleta.suelo;
        ctx.beginPath();
        ctx.moveTo(0, H - 70);
        for (let x = 0; x <= W; x += 40) {
            ctx.lineTo(x, H - 70 + Math.sin(x * 0.02) * 8);
        }
        ctx.lineTo(W, H); ctx.lineTo(0, H); ctx.closePath(); ctx.fill();

        // Cesped
        ctx.fillStyle = estado === 'bajo' ? '#4e342e' : '#2e7d32';
        ctx.globalAlpha = 0.5;
        ctx.fillRect(0, H - 60, W, 60);
        ctx.globalAlpha = 1;
    }

    function tallo(p) {
        const sway = Math.sin(t * 0.03 + p.fase) * (p.marchita ? 3 : 8) * (0.4 + factor);
        ctx.strokeStyle = p.marchita ? '#6d4c41' : paleta.planta;
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.moveTo(p.x, p.base);
        ctx.quadraticCurveTo(p.x + sway, p.base - p.alto * 0.6, p.x + sway * 1.6, p.base - p.alto);
        ctx.stroke();

        // Hojas
        ctx.fillStyle = p.marchita ? '#795548' : paleta.planta;
        const hojas = Math.round(2 + factor * 4);
        for (let i = 1; i <= hojas; i++) {
            const hy = p.base - (p.alto * i) / (hojas + 1);
            const lado = i % 2 === 0 ? 1 : -1;
            ctx.save();
            ctx.translate(p.x + sway * (i / hojas), hy);
            ctx.rotate(lado * (p.marchita ? 0.9 : 0.5));
            ctx.beginPath();
            ctx.ellipse(lado * 14, 0, 16, 7, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        const topX = p.x + sway * 1.6;
        const topY = p.base - p.alto;

        // Flor o semilla en la punta
        if (p.semilla) {
            ctx.fillStyle = '#8d6e63';
            ctx.beginPath(); ctx.arc(topX, topY, 6, 0, Math.PI * 2); ctx.fill();
        } else if (p.marchita) {
            ctx.fillStyle = '#5d4037';
            ctx.beginPath(); ctx.arc(topX, topY, 7, 0, Math.PI * 2); ctx.fill();
        } else if (factor > 0.25) {
            // petalos
            const escala = 0.7 + factor;
            const colores = ['#e91e63', '#f06292', '#ffb300', '#ab47bc', '#ef5350'];
            const color = colores[p.x % colores.length];
            ctx.fillStyle = color;
            for (let k = 0; k < 6; k++) {
                const a = (Math.PI * 2 * k) / 6;
                ctx.beginPath();
                ctx.ellipse(topX + Math.cos(a) * 8, topY + Math.sin(a) * 8,
                            8 * escala, 5 * escala, a, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.fillStyle = '#ffeb3b';
            ctx.beginPath(); ctx.arc(topX, topY, 5 * escala, 0, Math.PI * 2); ctx.fill();
        }
    }

    function fruto(x, y) {
        ctx.fillStyle = '#e53935';
        ctx.beginPath(); ctx.arc(x, y, 9, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = 'rgba(255,255,255,.5)';
        ctx.beginPath(); ctx.arc(x - 3, y - 3, 2.4, 0, Math.PI * 2); ctx.fill();
    }

    function volador(p) {
        p.x += p.vx;
        p.y += Math.sin(t * 0.05 + p.fase) * 1.4;
        if (p.x > W + 20) p.x = -20;
        if (p.x < -20) p.x = W + 20;

        if (p.tipo === 'ave') {
            ctx.strokeStyle = '#212121';
            ctx.lineWidth = 2.5;
            const a = Math.sin(t * 0.15 + p.fase) * 0.6;
            ctx.beginPath();
            ctx.moveTo(p.x - 10 * p.escala, p.y);
            ctx.lineTo(p.x, p.y - 6 * p.escala * a - 3);
            ctx.lineTo(p.x + 10 * p.escala, p.y);
            ctx.stroke();
        } else if (p.tipo === 'abeja') {
            ctx.fillStyle = '#ffc107';
            ctx.beginPath(); ctx.arc(p.x, p.y, 4 * p.escala, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#212121';
            ctx.fillRect(p.x - 5 * p.escala, p.y - 1, 10 * p.escala, 2);
            ctx.strokeStyle = 'rgba(255,255,255,.6)';
            ctx.beginPath(); ctx.ellipse(p.x, p.y - 5, 6 * p.escala, 3, 0, 0, Math.PI * 2); ctx.stroke();
        } else if (p.tipo === 'plaga') {
            ctx.fillStyle = '#33691e';
            ctx.beginPath(); ctx.ellipse(p.x, p.y, 7, 4, 0, 0, Math.PI * 2); ctx.fill();
            ctx.strokeStyle = '#33691e';
            ctx.lineWidth = 1;
            for (let l = -1; l <= 1; l += 2) {
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                ctx.lineTo(p.x + l * 6, p.y - 6);
                ctx.stroke();
            }
        }
    }

    let contadorFrutos = 0;
    // Posiciones fijas de frutos (persisten entre frames)
    const frutos = [];
    (function prepararFrutos() {
        for (let i = 0; i < nFrutos; i++) {
            const p = plantas[Math.floor(Math.random() * plantas.length)];
            if (p && !p.marchita) {
                frutos.push({ planta: p, dx: Math.random() * 20 - 10 });
            }
        }
    })();

    function pintarFrutos() {
        frutos.forEach((f) => {
            const p = f.planta;
            const sway = Math.sin(t * 0.03 + p.fase) * (0.4 + factor) * 8;
            const topX = p.x + sway * 1.6;
            const topY = p.base - p.alto;
            fruto(topX + f.dx, topY + 26);
        });
    }

    function frame() {
        t++;
        fondo();
        plantas.forEach(tallo);
        pintarFrutos();
        particulas.forEach(volador);
        requestAnimationFrame(frame);
    }
    frame();
})();
