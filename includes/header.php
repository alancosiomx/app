<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>WebApp Soporte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Menú superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">WebApp Soporte</span>
        <div class="d-flex">
            <span class="navbar-text me-3">
                <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? ''); ?>
                (<?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? ''); ?>)
            </span>
            <a class="btn btn-outline-light btn-sm" href="/logout.php">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="content-wrapper">
