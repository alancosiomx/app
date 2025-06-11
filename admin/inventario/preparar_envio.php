<?php
require_once __DIR__ . '/../init.php';

if (!tienePermiso('asignar_inventario')) {
    die('⛔ Acceso denegado');
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_preparar_envio.php';

require_once __DIR__ . '/../layout.php';
