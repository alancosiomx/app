<?php
require_once dirname(__DIR__) . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_ajustes.php';

require_once dirname(__DIR__) . '/layout.php';
