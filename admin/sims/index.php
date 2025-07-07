<?php
// app/admin/sims/index.php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php'; // Tabs y configuración

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Determina la vista activa (por default: 'inventario')
$vista = $_GET['vista'] ?? 'inventario';

// Define el mapa de vistas
$mapa_vistas = [
    'inventario' => __DIR__ . '/contenido_inventario.php',
    'logs'       => __DIR__ . '/contenido_logs.php'
];

// Determina el archivo a cargar, o fallback
$contenido = $mapa_vistas[$vista] ?? __DIR__ . '/contenido_inventario.php';

// Carga layout general del admin (usa $contenido como include)
require_once __DIR__ . '/../layout.php';
