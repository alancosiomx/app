<?php
require_once __DIR__ . '/../init.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_index.php';

require_once __DIR__ . '/../layout.php';
