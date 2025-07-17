<?php
require_once __DIR__ . '/init.php';

// Asegurar sesión válida
if (!isset($_SESSION['usuario_id'])) {
  header("Location: /login.php");
  exit;
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Técnico';

// KPIs falsos por ahora
$citas_hoy = 0;
$servicios_vim = 0;
$servicios_premium = 0;

// Ruta del contenido a mostrar en el layout
$contenido = __DIR__ . '/bloques/dashboard_contenido.php';

require_once __DIR__ . '/layout.php';
