<?php
require_once __DIR__ . '/../init.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

// Tabs disponibles
$tabs = [
    'resumen'        => 'Resumen',
    'asignar'        => 'Asignar',
    'editar'         => 'Editar',
    'devolver'       => 'Devoluciones',
    'recibir_danado' => 'Dañados'
];

// Tab activo y tipo
$tab = $_GET['tab'] ?? 'resumen';
$tipo = $_GET['tipo'] ?? 'tpv';

if (!array_key_exists($tab, $tabs) || !in_array($tipo, ['tpv', 'sim'])) {
    header("Location: index.php?tab=resumen&tipo=tpv");
    exit();
}

// Variables que estarán disponibles para todos los `contenido_*.php`
$contenido = __DIR__ . "/contenido_$tab.php";

require_once __DIR__ . '/../layout.php';
