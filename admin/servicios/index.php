<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/service_functions.php';

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Tabs disponibles
$tabs = [
    'por_asignar' => 'Por Asignar',
    'en_ruta'     => 'En Ruta',
    'concluido'   => 'Concluido',
    'citas'       => 'Citas',
];

// Tab activo
$tab = $_GET['tab'] ?? 'por_asignar';

$contenido_tab = match($tab) {
    'en_ruta'   => __DIR__ . '/contenido_en_ruta.php',
    'concluido' => __DIR__ . '/contenido_concluido.php',
    'citas'     => __DIR__ . '/contenido_citas.php',
    default     => __DIR__ . '/contenido_por_asignar.php',
};

// Guardamos la ruta a incluir directamente (el layout se encarga del include)
$contenido = $contenido_tab;

require_once __DIR__ . '/../layout.php';
