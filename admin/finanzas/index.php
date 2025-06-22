<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/constants.php'; // 👈 Asegura que exista la constante

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$vista = $_GET['vista'] ?? 'cobros';
$permitidas = ['cobros', 'pagos', 'viaticos', 'historial'];
if (!in_array($vista, $permitidas)) $vista = 'cobros';

$contenido = __DIR__ . '/' . $vista . '.php';

require_once __DIR__ . '/../layout.php';
