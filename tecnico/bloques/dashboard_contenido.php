<?php
// Simulación de datos (luego se conectará a la BD)
$citas_hoy = 3;
$servicios_vim = 2;
$servicios_premium = 1;
?>

<!-- Tarjeta Morada -->
<div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-2xl p-5 mb-6 shadow">
  <div class="text-sm">Resumen de hoy</div>
  <div class="mt-2 space-y-1 text-lg font-medium">
    <p>📅 Citas: <span class="font-bold"><?= $citas_hoy ?></span></p>
    <p>⚡ VIM pendientes: <span class="font-bold"><?= $servicios_vim ?></span></p>
    <p>💎 Premium: <span class="font-bold"><?= $servicios_premium ?></span></p>
  </div>
</div>

<!-- Grid Accesos -->
<div class="grid grid-cols-2 gap-4 mb-6">
  <a href="mis_servicios.php" class="bg-white rounded-xl shadow text-center p-4 text-sm hover:bg-gray-100">
    <div class="text-blue-600 text-2xl mb-1">🧾</div>
    Servicios
  </a>

  <a href="inventario.php" class="bg-white rounded-xl shadow text-center p-4 text-sm hover:bg-gray-100">
    <div class="text-green-600 text-2xl mb-1">📦</div>
    Inventario
  </a>

  <?php if ($_SESSION['puede_viaticos'] ?? false): ?>
    <a href="viaticos.php" class="bg-white rounded-xl shadow text-center p-4 text-sm hover:bg-gray-100">
      <div class="text-yellow-500 text-2xl mb-1">💰</div>
      Viáticos
    </a>
  <?php endif; ?>

  <a href="descargar_hs.php" class="bg-white rounded-xl shadow text-center p-4 text-sm hover:bg-gray-100">
    <div class="text-purple-500 text-2xl mb-1">📄</div>
    Hoja de Servicio
  </a>
</div>
