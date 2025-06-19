<?php
require_once __DIR__ . '/../init.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Detectar tab activo
$tab = $_GET['tab'] ?? 'por_asignar';

// Asignar el archivo de contenido correspondiente
$contenido = match($tab) {
    'en_ruta'    => __DIR__ . '/contenido_en_ruta.php',
    'concluido'  => __DIR__ . '/contenido_concluido.php',
    'citas'      => __DIR__ . '/contenido_citas.php',
    default      => __DIR__ . '/contenido_por_asignar.php'
};

// Incluir el layout general (wrapper HTML)
require_once __DIR__ . '/../layout.php';
