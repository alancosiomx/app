<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios POR ASIGNAR
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Técnicos activos
$tecnicos = $pdo->query("
    SELECT u.id, u.nombre
    FROM usuarios u
    JOIN usuarios_roles r ON r.usuario_id = u.id
    WHERE r.rol = 'idc' AND u.activo = 1
")->fetchAll(PDO::FETCH_ASSOC);

// Conteos
$total = count($servicios);
$citasHoy = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d'));
$citasManiana = array_filter($servicios, fn($s) => $s['fecha_cita'] === date('Y-m-d', strtotime('+1 day')));
?>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<!-- Resumen -->
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

<!-- Formulario de asignación múltiple -->
<form action="asignar_tecnico.php" method="post">
  <div class="flex justify-between items-center mb-3">
    <div class="flex items-center gap-2">
      <label for="tecnico_id" class="text-sm font-medium">Asignar a:</label>
      <select name="tecnico_id" id="tecnico_id" class="border rounded px-3 py-1 text-sm" required>
        <option value="">Selecciona un técnico</option>
        <?php foreach ($tecnicos as $t): ?>
          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">
      Asignar Seleccionados
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full table-auto bg-white shadow rounded-lg">
      <thead class="bg-gray-100 text-gray-700 text-sm">
        <tr>
          <th class="px-4 py-2"><input type="checkbox" id="checkAll" onclick="toggleAll(this)"></th>
          <th class="px-4 py-2 text-left">Ticket</th>
          <th class="px-4 py-2">Afiliación</th>
          <th class="px-4 py-2">Comercio</th>
          <th class="px-4 py-2">Ciudad</th>
          <th class="px-4 py-2">Servicio</th>
          <th class="px-4 py-2">Comentarios</th>
          <th class="px-4 py-2 text-center">🔍</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($servicios as $s): ?>
          <tr class="border-t text-sm hover:bg-gray-50">
            <td class="px-4 py-2 text-center">
              <input type="checkbox" name="tickets[]" value="<?= $s['ticket'] ?>">
            </td>
            <td class="px-4 py-2 font-medium text-blue-600"><?= htmlspecialchars($s['ticket']) ?></td>
            <td class="px-4 py-2 text-center"><?= htmlspecialchars($s['afiliacion']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['comercio']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['ciudad']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['servicio']) ?></td>
            <td class="px-4 py-2 text-gray-500 text-xs">
              <?= nl2br(htmlspecialchars($s['comentarios'] ?? '—')) ?>
            </td>
            <td class="px-4 py-2 text-center">
              <a href="#" class="ver-detalle text-blue-500 hover:underline" data-ticket="<?= $s['ticket'] ?>">🔍</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<script>
function toggleAll(source) {
  document.querySelectorAll('input[name="tickets[]"]').forEach(checkbox => {
    checkbox.checked = source.checked;
  });
}
</script>
