<?php
require_once __DIR__ . '/init.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/menu.php';


<div class="main-content" style="margin-top: 80px;">
    <div class="alert alert-success">
        👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.
    </div>
<?php echo "🚀 Cambios desde GitHub a las " . date("H:i:s"); ?>

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
<!-- Deploy automático funcionando: <?php echo date("Y-m-d H:i:s"); ?> -->

require_once __DIR__ . '/../includes/footer.php';
