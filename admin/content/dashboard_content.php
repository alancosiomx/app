<div class="alert alert-success">
    👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.
</div>

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
