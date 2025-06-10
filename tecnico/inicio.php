<?php
// tecnico/inicio.php
session_start();
// Aquí podrías validar si el técnico ha iniciado sesión

$nombre_tecnico = $_SESSION['usuario_nombre'] ?? 'Técnico';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio Técnico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Header -->
  <header class="bg-white shadow p-4 text-center text-xl font-semibold">
    👋 Bienvenido, <?= htmlspecialchars($nombre_tecnico) ?>
  </header>

  <!-- Dashboard de botones -->
  <main class="p-4 space-y-4">

    <!-- Mis Servicios -->
    <a href="mis_servicios.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">📋 Mis Servicios</span>
        <span class="text-sm text-gray-500">Ver asignaciones</span>
      </div>
    </a>

    <!-- Cerrar Servicio -->
    <a href="cerrar_servicio.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">✅ Cerrar Servicio</span>
        <span class="text-sm text-gray-500">Éxito / Rechazo</span>
      </div>
    </a>

    <!-- Agendar Cita -->
    <a href="agendar_cita.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">📅 Agendar Cita</span>
        <span class="text-sm text-gray-500">Reprogramar visita</span>
      </div>
    </a>

    <!-- Generar HS -->
    <a href="generar_hs.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">🧾 Generar Hoja de Servicio</span>
        <span class="text-sm text-gray-500">Exportar PDF</span>
      </div>
    </a>

    <!-- Descargar HS -->
    <a href="descargar_hs.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">📥 Descargar HS</span>
        <span class="text-sm text-gray-500">PDF ya generados</span>
      </div>
    </a>

    <!-- Solicitar Viáticos -->
    <a href="solicitar_viaticos.php" class="block bg-white p-4 rounded-2xl shadow hover:bg-gray-50 transition">
      <div class="flex items-center justify-between">
        <span class="text-lg font-semibold">💰 Solicitar Viáticos</span>
        <span class="text-sm text-gray-500">Gastos </span>
      </div>
    </a>

  </main>

</body>
</html>
