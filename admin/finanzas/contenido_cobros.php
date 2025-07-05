<?php
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$tecnico = $_GET['tecnico'] ?? '';
?>

<h2 class="text-lg font-semibold text-blue-700 mb-4">💳 Reporte de Cobros</h2>

<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <input type="hidden" name="vista" value="cobros">

  <div>
    <label class="block text-sm font-medium text-gray-600">Desde</label>
    <input type="date" name="desde" value="<?= $desde ?>" class="w-full border-gray-300 rounded shadow-sm">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Hasta</label>
    <input type="date" name="hasta" value="<?= $hasta ?>" class="w-full border-gray-300 rounded shadow-sm">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico</label>
    <select name="tecnico" class="w-full border-gray-300 rounded shadow-sm">
      <option value="">Todos</option>
      <?php
      $tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
      foreach ($tecnicos as $t) {
        $sel = ($t === $tecnico) ? 'selected' : '';
        echo "<option value='$t' $sel>$t</option>";
      }
      ?>
    </select>
  </div>

  <div class="flex items-end">
    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Filtrar</button>
  </div>
</form>

<?php
if ($desde && $hasta) {
  $f1 = $desde . ' 00:00:00';
  $f2 = $hasta . ' 23:59:59';

  $params = [$f1, $f2];
  $sql = "SELECT * FROM servicios_omnipos WHERE resultado IN ('Exito','Rechazo') AND fecha_atencion BETWEEN ? AND ?";

  if ($tecnico) {
    $sql .= " AND idc = ?";
    $params[] = $tecnico;
  }

  $sql .= " ORDER BY fecha_atencion DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $servicios = $stmt->fetchAll();

  if (count($servicios) === 0) {
    echo "<div class='bg-yellow-50 text-yellow-800 p-4 rounded'>No se encontraron visitas en ese rango.</div>";
  } else {
    echo "<table class='min-w-full mt-4 text-sm'><thead><tr>
    <th class='text-left px-2 py-1'>Ticket</th>
    <th class='text-left px-2 py-1'>IDC</th>
    <th class='text-left px-2 py-1'>Servicio</th>
    <th class='text-left px-2 py-1'>Resultado</th>
    <th class='text-left px-2 py-1'>Fecha</th>
    </tr></thead><tbody>";
    foreach ($servicios as $s) {
      echo "<tr>
        <td class='px-2 py-1'>{$s['ticket']}</td>
        <td class='px-2 py-1'>{$s['idc']}</td>
        <td class='px-2 py-1'>{$s['servicio']}</td>
        <td class='px-2 py-1'>{$s['resultado']}</td>
        <td class='px-2 py-1'>{$s['fecha_atencion']}</td>
      </tr>";
    }
    echo "</tbody></table>";
  }
}
  
function calcular_pago($pdo, $serv) {
    $idc = trim($serv['idc']);
    $servicio = trim($serv['servicio']);
    $resultado = trim($serv['resultado']);
    $banco = strtoupper(trim($serv['banco'])); // Asegura que sea BBVA, BANREGIO o AZTECA

    $stmt = $pdo->prepare("SELECT monto FROM precios_idc 
        WHERE idc = ? AND servicio = ? AND resultado = ? AND banco = ?");
    $stmt->execute([$idc, $servicio, $resultado, $banco]);
    
    return $stmt->fetchColumn() ?: 0;
}
?>
