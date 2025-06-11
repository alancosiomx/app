<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel OMNIPOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap CSS desde CDN confiable -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Estilos personalizados -->
  <style>
    body {
      margin: 0;
      background-color: #f9fafb;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .sidebar {
      height: 100vh;
      width: 220px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #1e293b;
      padding-top: 60px;
      color: white;
      z-index: 1000;
    }

    .sidebar h4 {
      text-align: center;
      margin-bottom: 1rem;
      font-weight: bold;
      color: #ffffff;
    }

    .sidebar a {
      color: #e5e7eb;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
      font-size: 15px;
    }

    .sidebar a:hover {
      background-color: #334155;
    }

    /* Responsive: Sidebar encima del contenido en móvil */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        padding-top: 10px;
      }
    }

    .main-content {
      margin-left: 220px;
      padding: 100px 40px 40px 40px;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 120px 20px 20px 20px;
      }
    }

    .top-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: #f1f5f9;
      color: #0f172a;
      padding: 12px 20px;
      border-bottom: 1px solid #e2e8f0;
      z-index: 1050;
      font-size: 15px;
    }
  </style>
</head>
<body>

<?php
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<!-- Barra superior con saludo -->
<div class="top-bar">
  👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.
</div>
