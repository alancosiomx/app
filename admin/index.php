<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Detectar tab activo
$tab = $_GET['tab'] ?? 'por_asignar';

// Switch para contenido por tab
$isMobile = preg_match('/(android|iphone|ipad)/i', $_SERVER['HTTP_USER_AGENT']);

$contenido = match ($tab) {
    'en_ruta'    => __DIR__ . '/contenido_en_ruta.php',
    'concluido'  => __DIR__ . '/contenido_concluido.php',
    'citas'      => __DIR__ . '/contenido_citas.php',
    default      => $isMobile
        ? __DIR__ . '/contenido_por_asignar_cards.php'
        : __DIR__ . '/contenido_por_asignar_tabla.php'
};

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

require_once __DIR__ . '/layout.php';
