<?php
// Seguridad antes de cualquier output
header_remove('X-Powered-By');
ini_set('expose_php', 'Off');

require_once __DIR__ . '/../../config.php';

session_start();

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// ¿Qué tab estamos cargando?
$tab = $_GET['tab'] ?? 'por_asignar';

if (!defined('INIT')) {
    require_once dirname(__DIR__, 2) . '/config.php';
}
require INIT;
?>

<div class="main-content">
    <h1>Gestión de Servicios</h1>

    <!-- Tabs de navegación -->
    <ul class="nav nav-tabs mb-4">

<h1>Gestión de Servicios</h1>

<!-- Tabs de navegación -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'por_asignar' ? 'active' : '' ?>" href="servicios.php?tab=por_asignar">Por Asignar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'en_ruta' ? 'active' : '' ?>" href="servicios.php?tab=en_ruta">En Ruta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'concluido' ? 'active' : '' ?>" href="servicios.php?tab=concluido">Concluido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'agendar_cita' ? 'active' : '' ?>" href="servicios.php?tab=agendar_cita">Agendar Cita</a>
    </li>
</ul>

<div>
<?php
// Incluir el tab correspondiente
switch ($tab) {
    case 'en_ruta':
        include 'en_ruta.php';
        break;
    case 'concluido':
        include 'concluido.php'; // <-- Asegúrate de crear este archivo
        break;
    case 'agendar_cita':
        include 'agendar_cita.php';
        break;
    case 'por_asignar':
    default:
        include 'por_asignar.php';
        break;
}
?>
</div>


