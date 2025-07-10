<?php
require_once __DIR__ . '/../init.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>

<div class="mb-4 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= ($_GET['vista'] ?? 'cobros') === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<div class="bg-white shadow p-6 rounded-2xl">
  <h1 class="text-xl font-bold mb-4 text-blue-700">💳 Reporte de Cobros</h1>

  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <input type="hidden" name="vista" value="cobros">

    <div>
      <label class="block text-sm font-medium text-gray-600">Desde</label>
      <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-600">Hasta</label>
      <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-600">Técnico</label>
      <select name="tecnico" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="">Todos</option>
        <?php
        $tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tecnicos as $t):
          $selected = ($_GET['tecnico'] ?? '') === $t ? 'selected' : '';
          echo "<option value='$t' $selected>$t</option>";
        endforeach;
        ?>
      </select>
    </div>

    <div class="flex items-end">
      <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
        Generar Reporte
      </button>
    </div>
  </form>

  <?php
  if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
    $desde = $_GET['desde'] . ' 00:00:00';
    $hasta = $_GET['hasta'] . ' 23:59:59';
    $tecnico = $_GET['tecnico'] ?? '';

    $sql = "SELECT * FROM servicios_omnipos 
            WHERE resultado IN ('Exito', 'Rechazo') 
            AND fecha_atencion BETWEEN ? AND ?";

    $params = [$desde, $hasta];

    if (!empty($tecnico)) {
        $sql .= " AND idc = ?";
        $params[] = $tecnico;
    }

    $sql .= " ORDER BY fecha_atencion DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($servicios) > 0):
  ?>

  <table class="min-w-full divide-y divide-gray-200 mt-6 bg-white rounded-xl shadow overflow-hidden">
    <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
      <tr>
        <th class="px-4 py-2 text-left">Ticket</th>
        <th class="px-4 py-2 text-left">Técnico</th>
        <th class="px-4 py-2 text-left">Servicio</th>
        <th class="px-4 py-2 text-left">Resultado</th>
        <th class="px-4 py-2 text-left">SLA</th>
        <th class="px-4 py-2 text-left">Fecha</th>
        <th class="px-4 py-2 text-left">Pago</th>
        <th class="px-4 py-2 text-left">Estado</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 text-sm">
      <?php
      foreach ($servicios as $serv) {
        $ticket = $serv['ticket'];
        $idc = $serv['idc'];

        $sla = ($serv['fecha_atencion'] && $serv['fecha_limite'] && strtotime($serv['fecha_atencion']) <= strtotime($serv['fecha_limite'])) ? 'DT' : 'FT';
        $estado_pago = $serv['pago_generado'] ? 'Pagado' : 'Pendiente';
        $pago_final = calcular_pago($pdo, $serv);
      ?>
      <tr>
        <td class="px-4 py-2 font-mono text-blue-700"><?= $ticket ?></td>
        <td class="px-4 py-2"><?= $idc ?></td>
        <td class="px-4 py-2"><?= $serv['servicio'] ?></td>
        <td class="px-4 py-2"><?= $serv['resultado'] ?></td>
        <td class="px-4 py-2"><?= $sla ?></td>
        <td class="px-4 py-2"><?= $serv['fecha_atencion'] ?></td>
        <td class="px-4 py-2">$<?= number_format($pago_final, 2) ?></td>
        <td class="px-4 py-2">
          <span class="inline-block <?= $estado_pago === 'Pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> text-xs px-2 py-1 rounded">
            <?= $estado_pago ?>
          </span>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <h2 class="text-lg font-semibold mt-10 mb-4 text-red-700">🧾 Rechazos Previos (visitas_servicios)</h2>

  <?php
  $rechazos_sql = "SELECT * FROM visitas_servicios 
                   WHERE resultado = 'Rechazo' 
                   AND fecha_visita BETWEEN ? AND ?";
  $rechazos_params = [$desde, $hasta];
  if (!empty($tecnico)) {
    $rechazos_sql .= " AND idc = ?";
    $rechazos_params[] = $tecnico;
  }
  $rechazos_sql .= " ORDER BY fecha_visita DESC";
  $rechazos_stmt = $pdo->prepare($rechazos_sql);
  $rechazos_stmt->execute($rechazos_params);
  $rechazos = $rechazos_stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($rechazos) > 0):
  ?>
  <table class="min-w-full divide-y divide-gray-200 mt-4 bg-white rounded-xl shadow overflow-hidden">
    <thead class="bg-red-50 text-xs font-semibold text-red-700">
      <tr>
        <th class="px-4 py-2 text-left">Ticket</th>
        <th class="px-4 py-2 text-left">Técnico</th>
        <th class="px-4 py-2 text-left">Fecha Visita</th>
        <th class="px-4 py-2 text-left">Pago</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 text-sm">
      <?php
      foreach ($rechazos as $visita) {
        $pago = calcular_pago($pdo, [
          'idc' => $visita['idc'],
          'servicio' => 'RECHAZO', // este valor debe ser ajustado si se requiere el tipo real de servicio
          'resultado' => 'Rechazo',
          'banco' => ''
        ]);
      ?>
      <tr>
        <td class="px-4 py-2 font-mono text-blue-700"><?= $visita['ticket'] ?></td>
        <td class="px-4 py-2"><?= $visita['idc'] ?></td>
        <td class="px-4 py-2"><?= $visita['fecha_visita'] ?></td>
        <td class="px-4 py-2">$<?= number_format($pago, 2) ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <?php else: ?>
    <div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4">No se encontraron rechazos en visitas para ese rango.</div>
  <?php endif; ?>

  <?php else: ?>
    <div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4">No se encontraron servicios para ese rango.</div>
  <?php endif; } ?>
</div>

<?php
function calcular_pago($pdo, $serv) {
  $stmt = $pdo->prepare("SELECT monto FROM precios_idc WHERE idc = ? AND servicio = ? AND resultado = ? AND banco = ?");
  $stmt->execute([
    $serv['idc'],
    $serv['servicio'],
    $serv['resultado'],
    $serv['banco'] ?? ''
  ]);
  return $stmt->fetchColumn() ?: 0;
}
?>
