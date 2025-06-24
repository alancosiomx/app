<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/service_functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tecnico_id = $_GET['tecnico_id'] ?? '';
$ticket_busqueda = $_GET['ticket'] ?? '';

$tecnicos = $pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 AND roles LIKE '%idc%'")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM servicios_omnipos WHERE estatus = 'Histórico' ";
$params = [];

if ($fecha_inicio) {
    $sql .= " AND fecha_atencion >= ? ";
    $params[] = $fecha_inicio . ' 00:00:00';
}
if ($fecha_fin) {
    $sql .= " AND fecha_atencion <= ? ";
    $params[] = $fecha_fin . ' 23:59:59';
}
if ($tecnico_id) {
    $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$tecnico_id]);
    $nombre_tecnico = $stmt->fetchColumn();
    $sql .= " AND idc = ? ";
    $params[] = $nombre_tecnico;
}
if ($ticket_busqueda) {
    $sql .= " AND (ticket LIKE ? OR afiliacion LIKE ?) ";
    $params[] = "%$ticket_busqueda%";
    $params[] = "%$ticket_busqueda%";
}

$sql .= " ORDER BY fecha_atencion DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

function safe($val) {
  return htmlspecialchars($val ?? '');
}
?>

<h1 class="text-2xl font-bold mb-4">Histórico de Servicios</h1>
<p class="text-sm text-gray-600 mb-6">Bienvenido, <strong><?= safe($_SESSION['usuario_nombre']) ?: 'Administrador' ?></strong></p>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<?php if (isset($_SESSION['mensaje'])): ?>
  <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= safe($_SESSION['mensaje']) ?></div>
  <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?= safe($_SESSION['error']) ?></div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="tab" value="concluido">

  <div>
    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha inicio</label>
    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= safe($fecha_inicio) ?>" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm">
  </div>

  <div>
    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha fin</label>
    <input type="date" id="fecha_fin" name="fecha_fin" value="<?= safe($fecha_fin) ?>" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm">
  </div>

  <div>
    <label for="tecnico_id" class="block text-sm font-medium text-gray-700">Técnico</label>
    <select id="tecnico_id" name="tecnico_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= $t['id'] ?>" <?= $tecnico_id == $t['id'] ? 'selected' : '' ?>><?= safe($t['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label for="ticket" class="block text-sm font-medium text-gray-700">Ticket o Afiliación</label>
    <input type="text" id="ticket" name="ticket" placeholder="Buscar..." value="<?= safe($ticket_busqueda) ?>" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm">
  </div>

  <div class="md:col-span-4 flex justify-end gap-2">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Filtrar</button>
    <a href="exportar_excel.php?<?= http_build_query($_GET) ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Exportar a Excel</a>
  </div>
</form>

<div class="overflow-x-auto bg-white rounded-xl shadow">
  <table class="min-w-full text-sm text-left text-gray-700">
    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
      <tr>
        <th class="px-4 py-3">Ticket</th>
        <th class="px-4 py-3">Afiliación</th>
        <th class="px-4 py-3">Comercio</th>
        <th class="px-4 py-3">Ciudad</th>
        <th class="px-4 py-3">Fecha Atención</th>
        <th class="px-4 py-3">Resultado</th>
        <th class="px-4 py-3">Comentarios</th>
        <th class="px-4 py-3 text-center">📃 HS</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      <?php foreach ($servicios as $s): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2"><?= safe($s['ticket']) ?></td>
          <td class="px-4 py-2"><?= safe($s['afiliacion']) ?></td>
          <td class="px-4 py-2"><?= safe($s['comercio']) ?></td>
          <td class="px-4 py-2"><?= safe($s['ciudad']) ?></td>
          <td class="px-4 py-2"><?= safe($s['fecha_atencion']) ?></td>
          <td class="px-4 py-2"><?= safe($s['resultado']) ?: '—' ?></td>
          <td class="px-4 py-2"><?= safe($s['comentarios']) ?></td>
          <td class="px-4 py-2 text-center">
            <a href="generar_hs.php?ticket=<?= urlencode($s['ticket']) ?>" target="_blank" class="text-blue-600 hover:underline">📃</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
