<?php
require_once '../auth.php';
require_once '../permisos.php';

if (!tienePermiso('ver_servicios_propios')) {
    die('⛔ Acceso denegado');
}

// Detectar pestaña activa
$tab = $_GET['tab'] ?? 'por_asignar';
$validTabs = ['por_asignar', 'en_ruta', 'concluido', 'agendar_cita'];
if (!in_array($tab, $validTabs)) {
    $tab = 'por_asignar';
}
if (!defined('INIT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require INIT;
?>

<div class="main-content">
    <h3 class="mb-4">Gestión de Servicios</h3>
    <style>
        .nav-link.active { font-weight: bold; }
    </style>
    <div class="container mt-4">

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'por_asignar' ? 'active' : '' ?>" href="?tab=por_asignar">📌 Por Asignar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'en_ruta' ? 'active' : '' ?>" href="?tab=en_ruta">🚗 En Ruta</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'concluido' ? 'active' : '' ?>" href="?tab=concluido">✅ Concluidos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'agendar_cita' ? 'active' : '' ?>" href="?tab=agendar_cita">📅 Agendar Cita</a>
        </li>
    </ul>

    <div class="card p-3 bg-white shadow-sm">
        <?php include "servicios/{$tab}.php"; ?>
    </div>
</div>

