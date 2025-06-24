<?php
require_once __DIR__ . '/../../config.php';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico = $_GET['idc'] ?? '';
$ticket = $_GET['ticket'] ?? '';

// Obtener técnicos únicos en ruta
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE estatus = 'En Ruta' AND idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

// Query base + filtros
$sql = "SELECT * FROM servicios_omnipos WHERE estatus = 'En Ruta'";
$params = [];

if ($fecha_inicio) {
    $sql .= " AND fecha_inicio >= ?";
    $params[] = $fecha_inicio . " 00:00:00";
}
if ($fecha_fin) {
    $sql .= " AND fecha_inicio <= ?";
    $params[] = $fecha_fin . " 23:59:59";
}
if ($tecnico) {
    $sql .= " AND idc = ?";
    $params[] = $tecnico;
}
if ($ticket) {
    $sql .= " AND (ticket LIKE ? OR afiliacion LIKE ?)";
    $params[] = "%$ticket%";
    $params[] = "%$ticket%";
}

$sql .= " ORDER BY fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<p class="text-gray-700 text-sm mb-4">Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></strong></p>
<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h2 class="text-xl font-semibold text-gray-800 mb-4">🛠 Servicios En Ruta</h2>

<!-- Formulario de filtros -->
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="tab" value="en_ruta">

  <div>
    <label class="text-sm font-medium text-gray-700">Fecha inicio</label>
    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Fecha fin</label>
    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Técnico</label>
    <select name="idc" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $nombre): ?>
        <option value="<?= htmlspecialchars($nombre) ?>" <?= $tecnico === $nombre ? 'selected' : '' ?>>
          <?= htmlspecialchars($nombre) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Ticket o Afiliación</label>
    <input type="text" name="ticket" value="<?= htmlspecialchars($ticket) ?>" placeholder="Buscar..." class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div class="md:col-span-4 text-right">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
      Filtrar
    </button>
  </div>
</form>

<!-- Tabla -->
<form method="POST" action="cerrar_servicio.php">
  <div class="flex justify-between items-center mb-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Aplicar resultado a los seleccionados:</label>
      <select name="resultado_servicio" required class="border rounded px-3 py-1 text-sm">
  <option value="">Selecciona resultado</option>
  <option value="ÉXITO">✅ ÉXITO</option>
  <option value="RECHAZO">❌ RECHAZO</option>
  <option value="REASIGNAR">🔁 REASIGNAR</option>
  <option value="CANCELADO">🚫 CANCELADO</option>
</select>

    </div>

    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
      Guardar Resultados
    </button>
  </div>

  <div class="overflow-x-auto bg-white shadow rounded-xl">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
        <tr>
          <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('input[name*=tickets]').forEach(c=>c.checked=this.checked)"></th>
          <th class="px-4 py-3">Ticket</th>
          <th class="px-4 py-3">Afiliación</th>
          <th class="px-4 py-3">Comercio</th>
          <th class="px-4 py-3">Ciudad</th>
          <th class="px-4 py-3">Servicio</th>
          <th class="px-4 py-3">Técnico</th>
          <th class="px-4 py-3">Comentarios</th>
          <th class="px-4 py-3 text-center">🔍</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php foreach ($servicios as $s): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-center">
              <input type="checkbox" name="tickets[]" value="<?= htmlspecialchars($s['ticket']) ?>">
            </td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['ticket']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['afiliacion']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['comercio']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['ciudad']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['servicio']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($s['idc']) ?></td>
            <td class="px-4 py-2 whitespace-pre-line"><?= nl2br(htmlspecialchars($s['comentarios'])) ?></td>
            <td class="px-4 py-2 text-center">
              <a href="#" class="ver-detalle text-blue-600 hover:underline" data-ticket="<?= $s['ticket'] ?>">🔍</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>


<!-- JS Validación -->
<script>
function toggleAll(source) {
  document.querySelectorAll('input[name="tickets[]"]').forEach(cb => cb.checked = source.checked);
}

function validarEnvio() {
  let errores = 0;
  document.querySelectorAll('input[name="tickets[]"]:checked').forEach(cb => {
    const ticket = cb.value;
    const select = document.querySelector(`select[name="resultados[${ticket}]"]`);
    if (!select || !select.value) {
      select.classList.add('border-red-500');
      errores++;
    } else {
      select.classList.remove('border-red-500');
    }
  });

  if (errores > 0) {
    alert("⚠️ Algunos tickets seleccionados no tienen resultado asignado.");
    return false;
  }

  return true;
}
</script>
