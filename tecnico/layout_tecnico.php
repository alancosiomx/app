<?php
// app/tecnico/layout_tecnico.php
session_start();
$nombre_tecnico = $_SESSION['usuario_nombre'] ?? 'Técnico';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Técnico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">

  <!-- Header fijo -->
  <header class="bg-white shadow p-4 text-center text-xl font-semibold sticky top-0 z-50">
    👋 Bienvenido, <?= htmlspecialchars($nombre_tecnico) ?>
  </header>

  <!-- Contenido principal -->
  <main class="p-4 space-y-4">

    <!-- Botón de regreso -->
    <div>
      <a href="index.php" class="inline-block text-blue-600 hover:underline text-sm">← Regresar al Panel</a>
    </div>

    <?php include $contenido; ?>

  </main>

</body>
</html>