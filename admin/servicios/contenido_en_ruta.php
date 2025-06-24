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

function safe($val) {
    return htmlspecialchars($val ?? '');
}
?>

<p class="text-gray-700 text-sm mb-4">Bienvenido, <strong><?= safe($_SESSION['usuario_nombre']) ?: 'Administrador' ?></strong></p>
<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h2 class="text-xl font-semibold text-gray-800 mb-4">🛠 Servicios En Ruta</h2>

<!-- Formulario de filtros -->
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="tab" value="en_ruta">

  <div>
    <label class="text-sm font-medium text-gray-700">Fecha inicio</label>
    <input type="date" name="fecha_inicio" value="<?= safe($fecha_inicio) ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Fecha fin</label>
    <input type="date" name="fecha_fin" value="<?= safe($fecha_fin) ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Técnico</label>
    <select name="idc" class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $nombre): ?>
        <option value="<?= safe($nombre) ?>" <?= $tecnico === $nombre ? 'selected' : '' ?>>
          <?= safe($nombre) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Ticket o Afiliación</label>
    <input type="text" name="ticket" value="<?= safe($ticket) ?>" placeholder="Buscar..." class="mt-1 w-full rounded border-gray-300 shadow-sm text-sm p-2">
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
        <option value="Exito">✅ ÉXITO</option>
        <option value="Rechazo">❌ RECHAZO</option>
        <option value="Reasignar">🔁 REASIGNAR</option>
        <option value="Cancelado">🚫 CANCELADO</option>
      </select>
    </div>

    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
      Guardar Resultados
    </button>
  </div>

  <div class="overflow-x-auto bg-white shadow rounded-xl">
    <table id="tabla-enruta" class="min-w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
        <tr>
          <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('input[name*=tickets]').forEach(c=>c.checked=this.checked)"></th>
          <th class="px-4 py-3">🏦 Banco</th>
          <th class="px-4 py-3">Ticket</th>
          <th class="px-4 py-3">📌 Extras</th>
          <th class="px-4 py-3">Afiliación</th>
          <th class="px-4 py-3">Comercio</th>
          <th class="px-4 py-3">Ciudad</th>
          <th class="px-4 py-3">Servicio</th>
          <th class="px-4 py-3">Técnico</th>
          <th class="px-4 py-3">📦 Insumos</th>
          <th class="px-4 py-3">Comentarios</th>
          <th class="px-4 py-3 text-center">🔍</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php foreach ($servicios as $s): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-center">
              <input type="checkbox" name="tickets[]" value="<?= safe($s['ticket']) ?>">
            </td>
            <td class="px-4 py-2"><?= safe($s['banco']) ?></td>
            <td class="px-4 py-2"><?= safe($s['ticket']) ?></td>
            <td class="px-4 py-2">
              <?php
                $vim = strtolower($s['vim'] ?? '');
                echo stripos($vim, '4 horas') !== false || stripos($vim, '4hrs') !== false ? '⚡' : '';
                echo stripos($vim, '24 horas') !== false || stripos($vim, '24hrs') !== false ? '⚡' : '';
                echo stripos($vim, 'premium') !== false ? '💎' : '';
                echo !empty($s['fecha_cita']) ? '🗓' : '';
              ?>
            </td>
            <td class="px-4 py-2"><?= safe($s['afiliacion']) ?></td>
            <td class="px-4 py-2"><?= safe($s['comercio']) ?></td>
            <td class="px-4 py-2"><?= safe($s['ciudad']) ?></td>
            <td class="px-4 py-2"><?= safe($s['servicio']) ?></td>
            <td class="px-4 py-2"><?= safe($s['idc']) ?></td>
            <td class="px-4 py-2"><?= safe($s['cantidad_insumos']) ?></td>
            <td class="px-4 py-2 whitespace-pre-line"><?= nl2br(safe($s['comentarios'])) ?></td>
            <td class="px-4 py-2 text-center">
              <a href="#" class="ver-detalle text-blue-600 hover:underline" data-ticket="<?= safe($s['ticket']) ?>">🔍</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<!-- JS DataTable y Validación -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
function toggleAll(source) {
  document.querySelectorAll('input[name="tickets[]"]').forEach(cb => cb.checked = source.checked);
}

$(document).ready(function () {
  $('#tabla-enruta').DataTable({
    pageLength: 100,
    order: [[2, 'desc']],
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron coincidencias",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      paginate: {
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
});
</script>
