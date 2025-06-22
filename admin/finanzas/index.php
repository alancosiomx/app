<?php
require_once __DIR__ . '/../../init.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Determina qué vista cargar
$vista = $_GET['vista'] ?? 'cobros';
$permitidas = ['cobros', 'pagos', 'viaticos', 'historial'];
if (!in_array($vista, $permitidas)) $vista = 'cobros';

$contenido = __DIR__ . '/' . $vista . '.php';

require_once __DIR__ . '/../layout.php';
