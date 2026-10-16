<?php
/**
 * includes/footer.php
 * ---------------------------------------------------------------------
 * Cierra el layout y carga los scripts comunes. El JavaScript consulta
 * periodicamente el API de notificaciones para actualizar el contador
 * en "tiempo real" (polling cada 8 segundos).
 * ---------------------------------------------------------------------
 */
?>
        </main>
    </div>
</div>

<!-- Raiz de avisos flotantes (toasts) -->
<div id="toasts" style="position:fixed;right:18px;bottom:18px;z-index:200;display:flex;flex-direction:column;gap:.6rem"></div>

<script>
    // Datos compartidos con el JavaScript
    window.NNR = {
        base: '<?= e(BASE_PATH) ?>',
        apiNotif: '<?= url('api/notificaciones.php') ?>',
        notifNoLeidas: <?= (int) ($usuario->notificacionesNoLeidas() ?? 0) ?>
    };
</script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
