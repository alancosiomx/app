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

  <!-- Filtros -->
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
    <th class="px-4 py-2 text-left">Visitas</th>
    <th class="px-4 py-2 text-left">Fecha de Visita</th>
  </tr>
</thead>

    <tbody class="divide-y divide-gray-100 text-sm">
      <?php
$agrupado = [];

foreach ($servicios as $serv) {
  $ticket = $serv['ticket'];
  $idc = $serv['idc'];

  // Obtener visitas previas de ese técnico (sin contar Éxito)
  $visitas_stmt = $pdo->prepare("
      SELECT fecha_visita FROM visitas_servicios 
      WHERE ticket = ? AND idc = ? AND resultado != 'Exito'
      ORDER BY fecha_visita ASC
  ");
  $visitas_stmt->execute([$ticket, $idc]);
  $fechas_visita = $visitas_stmt->fetchAll(PDO::FETCH_COLUMN);

  // Armar clave única por técnico + ticket
  $key = $ticket . '|' . $idc;

  if (!isset($agrupado[$key])) {
    $agrupado[$key] = [
      'ticket' => $ticket,
      'idc' => $idc,
      'servicio' => $serv['servicio'],
      'resultado' => $serv['resultado'],
      'sla' => ($serv['fecha_atencion'] && $serv['fecha_limite'] && strtotime($serv['fecha_atencion']) <= strtotime($serv['fecha_limite'])) ? 'DT' : 'FT',
      'fecha' => $serv['fecha_atencion'],
      'pago' => 0,
      'estado_pago' => $serv['pago_generado'] ? 'Pagado' : 'Pendiente',
      'visitas' => $fechas_visita
    ];
  }

  // Pago acumulado (por si más de una visita)
  $agrupado[$key]['pago'] += calcular_pago($pdo, $serv);
}

// Mostrar tabla
foreach ($agrupado as $row):
?>
  <tr>
    <td class="px-4 py-2 font-mono text-blue-700"><?= htmlspecialchars($row['ticket']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($row['idc']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($row['servicio']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($row['resultado']) ?></td>
    <td class="px-4 py-2"><?= $row['sla'] ?></td>
    <td class="px-4 py-2"><?= $row['fecha'] ?></td>
    <td class="px-4 py-2">$<?= number_format($row['pago'], 2) ?></td>
    <td class="px-4 py-2">
      <?php if ($row['estado_pago'] === 'Pagado'): ?>
        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Pagado</span>
      <?php else: ?>
        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendiente</span>
      <?php endif; ?>
    </td>
    <td class="px-4 py-2 text-center"><?= count($row['visitas']) ?: '' ?></td>
    <td class="px-4 py-2"><?= implode(', ', $row['visitas']) ?: '' ?></td>
  </tr>
<?php endforeach; ?>

    </tbody>
  </table>

  <?php else: ?>
    <div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4">No se encontraron servicios para ese rango.</div>
  <?php endif; } ?>
</div>

<?php
function calcular_pago($pdo, $serv) {
    $stmt = $pdo->prepare("SELECT monto FROM precios_idc 
        WHERE idc = ? AND servicio = ? AND resultado = ? AND banco = ?");
    $stmt->execute([
        $serv['idc'],
        $serv['servicio'],
        $serv['resultado'],
        $serv['banco']
    ]);
    return $stmt->fetchColumn() ?: 0;
}
?>
