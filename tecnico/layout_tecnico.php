<?php
session_start();
$nombre_tecnico = $_SESSION['usuario_nombre'] ?? 'Técnico';
$contenido = $contenido ?? null; // asegura que esté definida
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Técnico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 flex min-h-screen">

  <!-- Menú lateral -->
  <aside class="w-64 bg-white shadow-md hidden md:block">
    <div class="p-4 font-bold text-xl border-b border-gray-200">
      👨‍🔧 Técnico
    </div>
    <nav class="p-4 space-y-2">
      <a href="index.php" class="block text-blue-700 hover:underline">🏠 Inicio</a>
      <a href="mis_servicios.php" class="block hover:text-blue-600">🧾 Mis Servicios</a>
      <a href="viaticos.php" class="block hover:text-blue-600">💰 Viáticos</a>
      <a href="cerrar_servicio.php" class="block hover:text-blue-600">✅ Cerrar Servicio</a>
      <a href="logout.php" class="block text-red-600 hover:underline">🚪 Salir</a>
    </nav>
  </aside>

  <!-- Contenido principal -->
  <div class="flex-1 flex flex-col">
    <!-- Header superior -->
    <header class="bg-white shadow p-4 text-center text-lg font-semibold sticky top-0 z-40">
      👋 Bienvenido, <?= htmlspecialchars($nombre_tecnico) ?>
    </header>

    <main class="p-6 flex-1 overflow-y-auto">
      <?php
        if ($contenido && file_exists($contenido)) {
          include $contenido;
        } else {
          echo "<p class='text-gray-500'>Contenido no disponible.</p>";
        }
      ?>
    </main>
  </div>

</body>
</html>
