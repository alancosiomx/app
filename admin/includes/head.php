<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel OMNIPOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap -->
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
      transition: transform 0.3s ease-in-out;
      z-index: 1000;
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
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
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .top-bar .menu-toggle {
      display: none;
      font-size: 24px;
      background: none;
      border: none;
    }

    @media (max-width: 768px) {
      .top-bar .menu-toggle {
        display: block;
      }

      .sidebar {
        position: fixed;
        top: 52px;
        width: 100%;
        height: calc(100vh - 52px);
      }

      .main-content {
        padding: 100px 20px 20px 20px;
        margin-left: 0;
      }
    }

    .main-content {
      margin-left: 220px;
      padding: 100px 40px 40px 40px;
      transition: margin-left 0.3s ease-in-out;
    }

    @media (max-width: 768px) {
      .main-content.collapsed {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<?php $usuario = $_SESSION['usuario_nombre'] ?? 'Administrador'; ?>

<!-- Barra superior -->
<div class="top-bar">
  <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
  <span>👋 Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong>. Este es tu panel de administración.</span>
</div>

<!-- Script para toggle del sidebar -->
<script>
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('collapsed');
  }
</script>
