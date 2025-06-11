<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Cargar partes del layout
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/menu.php';
?>

<div class="main-content container mt-5">
    <div class="alert alert-success">
        👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.
    </div>

    <p><?php echo "🚀 Cambios desde GitHub a las " . date("H:i:s"); ?></p>

    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="/admin/carga.php" class="btn btn-outline-primary w-100">📂 Cargar Archivos</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="/admin/servicios.php" class="btn btn-outline-success w-100">📋 Gestión de Servicios</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="/admin/inventario.php" class="btn btn-outline-warning w-100">📦 Inventario</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="/admin/ajustes/" class="btn btn-outline-secondary w-100">⚙️ Ajustes</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="/admin/minidrive/" class="btn btn-outline-dark w-100">📁 MiniDrive</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot.php'; ?>
