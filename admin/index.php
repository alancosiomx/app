<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Cargar resumen por defecto
$contenido = __DIR__ . '/resumen_servicios.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

require_once __DIR__ . '/layout.php';
