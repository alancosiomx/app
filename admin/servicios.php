<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../permisos.php';

if (!tienePermiso('ver_servicios_propios')) {
    die('⛔ Acceso denegado');
}

// Detectar pestaña activa
$tab = $_GET['tab'] ?? 'por_asignar';
$validTabs = ['por_asignar', 'en_ruta', 'concluido', 'agendar_cita'];
if (!in_array($tab, $validTabs)) {
    $tab = 'por_asignar';
}

$contenido = __DIR__ . "/contenido_servicios.php";
require_once __DIR__ . '/layout.php';
