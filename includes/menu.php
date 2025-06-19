<!-- Menú lateral -->
<div class="sidebar p-3 bg-light" style="position: fixed; width: 250px; height: 100vh; overflow-y: auto;">
    <h5 class="mb-3">Menú</h5>
    <ul class="nav flex-column">

        <!-- SERVICIOS -->
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#submenuServicios" role="button">
                <i class="bi bi-tools me-2"></i>Servicios
            </a>
            <div class="collapse" id="submenuServicios">
                <ul class="nav flex-column ps-3">
                    <li><a class="nav-link" href="/servicios/carga.php">Carga</a></li>
                    <li><a class="nav-link" href="/servicios/control.php">Control</a></li>
                    <li><a class="nav-link" href="/servicios/historico.php">Histórico</a></li>
                </ul>
            </div>
        </li>

        <!-- INVENTARIO -->
        <li class="nav-item mt-2">
            <a class="nav-link" data-bs-toggle="collapse" href="#submenuInventario">
                <i class="bi bi-box-seam me-2"></i>Inventario
            </a>
            <div class="collapse" id="submenuInventario">
                <ul class="nav flex-column ps-3">
                    <li><a class="nav-link" href="/inventario/index.php">Inventario</a></li>
                    <li><a class="nav-link" href="/inventario/fabricantes.php">Fabricantes</a></li>
                    <li><a class="nav-link" href="/inventario/modelos.php">Modelos</a></li>
                </ul>
            </div>
        </li>

        <!-- AJUSTES -->
        <?php if (!empty($_SESSION['usuario_roles']) && in_array('admin', $_SESSION['usuario_roles'])): ?>
        <li class="nav-item mt-2">
            <a class="nav-link" data-bs-toggle="collapse" href="#submenuAjustes">
                <i class="bi bi-gear me-2"></i>Ajustes
            </a>
            <div class="collapse" id="submenuAjustes">
                <ul class="nav flex-column ps-3">
                    <li><a class="nav-link" href="/crud/usuarios/index.php">Usuarios</a></li>
                    <li><a class="nav-link" href="/crud/inventario/index.php">Inventario</a></li>
                </ul>
            </div>
        </li>
        <?php endif; ?>

    </ul>
</div>

<!-- Contenido principal -->
<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <!-- Tu contenido dinámico va aquí -->
</div>
