<?php
require_once __DIR__ . '/../init.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

// Tabs disponibles
$tabs = [
    'resumen'     => 'Resumen',
    'asignar'     => 'Asignar',
    'editar'      => 'Editar',
    'devolver'    => 'Devoluciones',
    'recibir_danado' => 'Dañados'
];

// Tab activo por default
$tab = $_GET['tab'] ?? 'resumen';
$tipo = $_GET['tipo'] ?? 'tpv';

if (!in_array($tipo, ['tpv', 'sim'])) {
    echo "<p class='text-red-600'>❌ Tipo inválido.</p>";
    return;
}

if (!array_key_exists($tab, $tabs)) {
    echo "<p class='text-red-600'>❌ Tab inválido.</p>";
    return;
}

// Ruta del archivo de contenido dinámico
$contenido = __DIR__ . "/contenido_{$tab}.php";

require_once __DIR__ . '/../layout.php';
