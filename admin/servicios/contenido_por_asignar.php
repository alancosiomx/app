<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios POR ASIGNAR
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos activos
$tecnicos = $pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 AND roles LIKE '%idc%'")->fetchAll(PDO::FETCH_ASSOC);

// Conteo para dashboard
$total = count($servicios);
$citasHoy = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d'));
$citasManiana = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d', strtotime('+1 day')));
?>

<!-- Dashboard resumen -->
<div class="grid grid-cols-3 gap-2 text-center mb-4">
  <div class="bg-blue-100 text-blue-800 p-3 rounded-xl">
    <div class="text-sm font-semibold">Total</div>
    <div class="text-xl font-bold"><?= $total ?></div>
  </div>
  <div class="bg-yellow-100 text-yellow-800 p-3 rounded-xl">
    <div class="text-sm font-semibold">Citas Hoy</div>
    <div class="text-xl font-bold"><?= count($citasHoy) ?></div>
  </div>
  <div class="bg-orange-100 text-orange-800 p-3 rounded-xl">
    <div class="text-sm font-semibold">Citas Mañana</div>
    <div class="text-xl font-bold"><?= count($citasManiana) ?></div>
  </div>
</div>

<!-- Lista tipo tarjetas -->
<div class="space-y-4">
  <?php foreach ($servicios as $s): ?>
    <form method="post" action="asignar_tecnico.php" class="bg-white shadow rounded-xl p-4">
      <div class="flex justify-between items-center">
        <h3 class="font-bold text-lg">🎫 <?= htmlspecialchars($s['ticket']) ?></h3>
        <?php if ($s['fecha_cita']): ?>
          <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">CITA</span>
        <?php endif; ?>
      </div>

      <p class="text-sm text-gray-600">
        🏪 <strong><?= htmlspecialchars($s['comercio']) ?></strong><br>
        📍 <?= htmlspecialchars($s['ciudad']) ?><br>
        🧾 <?= htmlspecialchars($s['servicio']) ?><br>
        💬 <?= nl2br(htmlspecialchars($s['comentarios'] ?? 'Sin comentarios')) ?>
      </p>

      <div class="flex flex-col gap-2 mt-3">
        <input type="hidden" name="ticket" value="<?= $s['ticket'] ?>">
        <select name="tecnico_id" required class="px-3 py-2 rounded-lg border border-gray-300">
          <option value="">Asignar técnico</option>
          <?php foreach ($tecnicos as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="flex gap-2">
          <button type="submit" class="text-sm px-3 py-1 bg-green-500 text-white rounded-full">✅ Asignar</button>
          <a href="#" class="ver-detalle text-sm px-3 py-1 bg-blue-100 text-blue-800 rounded-full" data-ticket="<?= $s['ticket'] ?>">🔍 Detalle</a>
        </div>
      </div>
    </form>
  <?php endforeach; ?>
</div>
