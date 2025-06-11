<?php
require_once __DIR__ . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_carga.php';

require_once __DIR__ . '/layout.php';
