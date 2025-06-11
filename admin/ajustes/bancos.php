<?php
require_once dirname(__DIR__) . '/init.php';

if (!tienePermiso('ver_inventario')) {
    die('⛔ Acceso denegado');
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_bancos.php';

require_once dirname(__DIR__) . '/layout.php';
