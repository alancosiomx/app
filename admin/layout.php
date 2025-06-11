<?php
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

if (!defined('INIT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require INIT;
?>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" style="margin-left: 250px;"> <!-- 250px porque el sidebar mide eso -->
    <div class="container-fluid">
        <span class="navbar-brand">OMNIPOS - Admin</span>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/admin/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container" style="margin-left: 250px; padding-top: 80px;"> <!-- 250px sidebar + margen header -->
    <?php include $contenido; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


