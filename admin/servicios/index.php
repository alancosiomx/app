<?php
require_once __DIR__ . '/../init.php';

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Detectar el tab activo (por_asignar, en_ruta, concluido, citas)
$tab = $_GET['tab'] ?? 'por_asignar';

// Cargar el contenido correspondiente
$contenido = match($tab) {
    'en_ruta'   => __DIR__ . '/contenido_en_ruta.php',
    'concluido' => __DIR__ . '/contenido_concluido.php',
    'citas'     => __DIR__ . '/contenido_citas.php',
    default     => __DIR__ . '/contenido_por_asignar.php',
};

// Renderizar layout
require_once __DIR__ . '/../layout.php';
