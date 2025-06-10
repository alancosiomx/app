<?php
require_once __DIR__ . '/init.php';



$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - OMNIPOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">OMNIPOS - Admin</span>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 80px;">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
