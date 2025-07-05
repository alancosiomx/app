<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php';

$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
$current_tab = $_GET['vista'] ?? 'cobros';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$tecnico = $_GET['tecnico'] ?? '';

// Consulta desde visitas_servicios y servicios_omnipos
$condiciones = ["v.resultado IN ('Exito', 'Rechazo')"];
$parametros = [];

if ($desde && $hasta) {
  $condiciones[] = "v.fecha_visita BETWEEN ? AND ?";
  $parametros[] = "$desde 00:00:00";
  $parametros[] = "$hasta 23:59:59";
}

if ($tecnico) {
  $condiciones[] = "v.idc = ?";
  $parametros[] = $tecnico;
}

$where = count($condiciones) ? "WHERE " . implode(' AND ', $condiciones) : "";
$sql = "SELECT v.*, s.comercio, s.servicio, s.ticket, s.fecha_limite
        FROM visitas_servicios v
        JOIN servicios_omnipos s ON s.ticket = v.ticket
        $where
        ORDER BY v.fecha_visita DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos únicos
$tecnicos = $pdo->query("SELECT DISTINCT idc FROM visitas_servicios WHERE idc IS NOT NULL ORDER BY idc")->fetchAll(PDO::FETCH_COLUMN);
?>
<!-- 🔗 TABS DE FINANZAS -->
<div class="mb-4 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= $current_tab === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<h1 class="text-xl font-bold mb-4 text-blue-700"><?= TABS_FINANZAS['cobros'] ?></h1>
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="vista" value="cobros">
  <div>
    <label class="block text-sm font-medium text-gray-600">Desde</label>
    <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>" class="w-full border-gray-300 rounded-md">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Hasta</label>
    <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>" class="w-full border-gray-300 rounded-md">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico</label>
    <select name="tecnico" class="w-full border-gray-300 rounded-md">
      <option value="">Todos</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= $t ?>" <?= $t === $tecnico ? 'selected' : '' ?>><?= $t ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex items-end">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Filtrar</button>
  </div>
</form>

<?php if (count($visitas) > 0): ?>
<table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow overflow-hidden text-sm">
  <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2 text-left">Ticket</th>
      <th class="px-4 py-2 text-left">Comercio</th>
      <th class="px-4 py-2 text-left">Técnico</th>
      <th class="px-4 py-2 text-left">Servicio</th>
      <th class="px-4 py-2 text-left">Resultado</th>
      <th class="px-4 py-2 text-left">SLA</th>
      <th class="px-4 py-2 text-left">Fecha de visita</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-100">
    <?php foreach ($visitas as $v):
      $sla = ($v['fecha_visita'] <= $v['fecha_limite']) ? 'DT' : 'FT';
    ?>
      <tr>
        <td class="px-4 py-2 font-mono text-blue-700"><?= htmlspecialchars($v['ticket']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['comercio']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['idc']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['servicio']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['resultado']) ?></td>
        <td class="px-4 py-2"><?= $sla ?></td>
        <td class="px-4 py-2"><?= $v['fecha_visita'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
  <div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4">No se encontraron visitas en ese rango.</div>
<?php endif; ?>
