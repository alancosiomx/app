<?php
// app/admin/sims/index.php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/constants.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$vista = $_GET['vista'] ?? 'inventario';

$mapa_vistas = [
    'inventario' => __DIR__ . '/contenido_inventario.php',
    'asignar'    => __DIR__ . '/contenido_asignar.php',
    'logs'       => __DIR__ . '/contenido_logs.php'
];

$contenido = $mapa_vistas[$vista] ?? __DIR__ . '/contenido_inventario.php';

// Render layout base
require_once __DIR__ . '/../layout.php';
