<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel OMNIPOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap moderno y liviano -->
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
      background-color: #1e293b; /* Gris oscuro corporativo */
      padding-top: 60px;
      color: white;
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

    .main-content {
      margin-left: 220px;
      padding: 30px 40px;
    }

    .alert {
      font-size: 16px;
    }

    .btn {
      font-weight: 500;
    }
  </style>
</head>
<body>
