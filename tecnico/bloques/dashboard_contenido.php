<?php
require_once __DIR__ . '/../init.php';

$idc = $_SESSION['usuario_nombre'] ?? ''; // Ajusta si usas otro identificador
$hoy = date('Y-m-d');

// 📅 Citas HOY (solo si están en ruta)
$stmt = $pdo->prepare("
  SELECT COUNT(*) FROM servicios_omnipos
  WHERE fecha_cita = ? AND idc = ? AND estatus = 'En Ruta'
");
$stmt->execute([$hoy, $idc]);
$citas_hoy = $stmt->fetchColumn();

// ⚡ VIM pendientes (en ruta y con texto VIM válido)
$stmt = $pdo->prepare("
  SELECT COUNT(*) FROM servicios_omnipos
  WHERE idc = ?
    AND estatus = 'En Ruta'
    AND (
      vim LIKE '%4 horas%' OR
      vim LIKE '%24 horas%'
    )
");
$stmt->execute([$idc]);
$servicios_vim = $stmt->fetchColumn();

// 💎 Premium (en ruta con palabra premium en vim)
$stmt = $pdo->prepare("
  SELECT COUNT(*) FROM servicios_omnipos
  WHERE idc = ?
    AND estatus = 'En Ruta'
    AND vim LIKE '%premium%'
");
$stmt->execute([$idc]);
$servicios_premium = $stmt->fetchColumn();
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
