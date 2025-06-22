<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios POR ASIGNAR
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos activos con join correcto
$tecnicos = $pdo->query("SELECT u.id, u.nombre FROM usuarios u JOIN usuarios_roles r ON r.usuario_id = u.id WHERE r.rol = 'idc' AND u.activo = 1")->fetchAll(PDO::FETCH_ASSOC);

// Conteo para dashboard
$total = count($servicios);
$citasHoy = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d'));
$citasManiana = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d', strtotime('+1 day')));
?>

<!-- Tabs de navegación -->
<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<!-- Dashboard resumen estilo horizontal -->
<div class="grid grid-cols-3 gap-4 mb-6">
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Total</div>
    <div class="text-2xl font-bold text-blue-600"><?= $total ?></div>
  </div>
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Citas Hoy</div>
    <div class="text-2xl font-bold text-yellow-600"><?= count($citasHoy) ?></div>
  </div>
  <div class="bg-white shadow p-4 rounded-lg text-center">
    <div class="text-gray-500 text-sm">Citas Mañana</div>
    <div class="text-2xl font-bold text-orange-600"><?= count($citasManiana) ?></div>
  </div>
</div>

<!-- Tabla de servicios estilo dashboard -->
<div class="overflow-x-auto">
  <table class="min-w-full table-auto bg-white shadow rounded-lg">
    <thead class="bg-gray-100 text-gray-700 text-sm">
      <tr>
        <th class="px-4 py-2 text-left">Ticket</th>
        <th class="px-4 py-2">Afiliación</th>
        <th class="px-4 py-2">Comercio</th>
        <th class="px-4 py-2">Ciudad</th>
        <th class="px-4 py-2">Servicio</th>
        <th class="px-4 py-2">Comentarios</th>
        <th class="px-4 py-2">Asignar</th>
        <th class="px-4 py-2">🔍</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($servicios as $s): ?>
        <tr class="border-t text-sm">
          <td class="px-4 py-2 font-medium text-blue-600"><?= htmlspecialchars($s['ticket']) ?></td>
          <td class="px-4 py-2 text-center"><?= htmlspecialchars($s['afiliacion']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['comercio']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['ciudad']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($s['servicio']) ?></td>
          <td class="px-4 py-2 text-gray-500 text-xs">
            <?= nl2br(htmlspecialchars($s['comentarios'] ?? '—')) ?>
          </td>
          <td class="px-4 py-2">
            <form action="asignar_tecnico.php" method="post" class="flex gap-2">
              <input type="hidden" name="ticket" value="<?= $s['ticket'] ?>">
              <select name="tecnico_id" class="border rounded px-2 py-1 text-sm" required>
                <option value="">Seleccionar</option>
                <?php foreach ($tecnicos as $t): ?>
                  <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs">Asignar</button>
            </form>
          </td>
          <td class="px-4 py-2 text-center">
            <a href="#" class="ver-detalle text-blue-500 hover:underline" data-ticket="<?= $s['ticket'] ?>">🔍</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
