<?php
/**
 * includes/header.php
 * ---------------------------------------------------------------------
 * Cabecera comun de las paginas privadas: barra lateral con el menu
 * del rol (polimorfismo: cada usuario arma su menu()) y barra superior
 * con la campana de notificaciones y el contador de no leidas.
 *
 * Variables esperadas:
 *   $usuario    -> objeto App\Models\Usuario (lo define includes/guard.php)
 *   $pageTitle  -> titulo de la pestana (opcional)
 *   $activo     -> url del item de menu activo (opcional)
 * ---------------------------------------------------------------------
 */

/** @var \App\Models\Usuario $usuario */
$pageTitle = $pageTitle ?? APP_NAME;
$activo    = $activo ?? '';
$noLeidas  = $usuario->notificacionesNoLeidas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/garden.css') ?>">
</head>
<body>
<div class="app">
    <!-- ============ BARRA LATERAL (menu por rol) ============ -->
    <aside class="sidebar" id="sidebar">
        <div class="brand"><?= e(APP_NAME) ?></div>
        <div class="brand-sub"><?= e(APP_ORG) ?> · ROCK & NATURALEZA</div>

        <div class="perfil-mini">
            <img src="<?= e($usuario->getFotoUrl()) ?>" alt="Foto de perfil">
            <div class="nombre"><?= e($usuario->getNombreCompleto()) ?></div>
            <div class="rol"><?= e($usuario->getRol()) ?></div>
        </div>

        <nav>
            <?php foreach ($usuario->menu() as $item): ?>
                <a class="nav-item <?= $activo === $item['url'] ? 'activo' : '' ?>"
                   href="<?= e($item['url']) ?>">
                    <span class="ico"><?= $item['icono'] ?></span>
                    <span><?= e($item['titulo']) ?></span>
                </a>
            <?php endforeach; ?>
            <a class="nav-item" href="<?= url('logout.php') ?>">
                <span class="ico">🚪</span><span>Cerrar sesion</span>
            </a>
        </nav>
    </aside>

    <!-- ============ AREA PRINCIPAL ============ -->
    <div class="main">
        <header class="topbar">
            <div class="flex center gap">
                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">☰</button>
                <span class="titulo"><?= e($pageTitle) ?></span>
            </div>
            <div class="topbar-acciones">
                <a class="campana" href="<?= url('notificaciones.php') ?>" title="Notificaciones">
                    🔔
                    <span class="contador" id="contadorNotif"
                          style="<?= $noLeidas === 0 ? 'display:none' : '' ?>">
                        <?= (int) $noLeidas ?>
                    </span>
                </a>
                <img src="<?= e($usuario->getFotoUrl()) ?>" alt="avatar"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--green-500)">
            </div>
        </header>

        <main class="content">
