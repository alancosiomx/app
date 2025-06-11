<?php
require_once __DIR__ . '/../init.php';

if (!tienePermiso('registrar_inventario')) {
    die('⛔ Acceso denegado');
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_editar.php';

require_once __DIR__ . '/../layout.php';
