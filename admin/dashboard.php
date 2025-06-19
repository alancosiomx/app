<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$contenido = __DIR__ . '/contenido_dashboard.php';

require_once __DIR__ . '/layout.php';
