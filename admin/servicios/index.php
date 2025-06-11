<?php
// Seguridad antes de cualquier output
header_remove('X-Powered-By');
ini_set('expose_php', 'Off');

require_once __DIR__ . '/../../config.php';
session_start();

// Verifica si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Determina qué tab cargar
$tab = $_GET['tab'] ?? 'por_asignar';

// Determina qué archivo incluir según el tab
$contenido = match($tab) {
    'en_ruta' => 'contenido_en_ruta.php',
    'concluido' => 'contenido_concluido.php',
    'citas' => 'contenido_citas.php',
    default => 'contenido_por_asignar.php'
};
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Servicios - OMNIPOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
    />
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Gestión de Servicios - OMNIPOS</h1>
        <p>Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong></p>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'por_asignar' ? 'active' : '' ?>" href="?tab=por_asignar">Por Asignar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'en_ruta' ? 'active' : '' ?>" href="?tab=en_ruta">En Ruta</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'concluido' ? 'active' : '' ?>" href="?tab=concluido">Histórico</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'citas' ? 'active' : '' ?>" href="?tab=citas">Citas</a>
            </li>
        </ul>

        <div class="tab-content">
            <?php include __DIR__ . '/' . $contenido; ?>
        </div>
    </div>
</body>
</html>
