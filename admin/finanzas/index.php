<?php
// app/admin/finanzas/index.php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../constants.php';

session_start();
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Definir la vista actual
$vista = $_GET['vista'] ?? 'cobros';

// Determinar qué contenido incluir
$contenido = match ($vista) {
    'pagos'     => __DIR__ . '/pagos.php',
    'viaticos'  => __DIR__ . '/viaticos.php',
    'historial' => __DIR__ . '/historial.php',
    'precios'   => __DIR__ . '/precios.php',
    default     => __DIR__ . '/cobros.php'
};

require_once __DIR__ . '/../layout.php';
