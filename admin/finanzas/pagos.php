<?php
require_once __DIR__ . '/../init.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

// Guardar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idc'])) {
  $idc = $_POST['idc'];
  $desde = $_POST['desde'] . ' 00:00:00';
  $hasta = $_POST['hasta'] . ' 23:59:59';
  $observaciones = $_POST['observaciones'] ?? '';
  $folio = $_POST['folio'] ?? '';

  $stmt = $pdo->prepare("UPDATE servicios_omnipos SET pago_generado = 1, fecha_pago = NOW(), observaciones_pago = ?, folio_pago = ? WHERE idc = ? AND resultado IN ('Exito', 'Rechazo') AND fecha_atencion BETWEEN ? AND ?");
  $stmt->execute([$observaciones, $folio, $idc, $desde, $hasta]);
  echo "<div class='bg-green-100 text-green-800 p-4 mb-4 rounded'>✅ Pago registrado correctamente para $idc</div>";
}

$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

<h1 class="text-xl font-bold mb-4 text-blue-700">💼 Pagos por Técnico</h1>

<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div>
    <label class="block text-sm font-medium text-gray-600">Desde</label>
    <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Hasta</label>
    <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
  </div>

  <div class="flex items-end">
    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
      Ver Pagos
    </button>
  </div>
</form>

<?php
if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
  $desde = $_GET['desde'] . ' 00:00:00';
  $hasta = $_GET['hasta'] . ' 23:59:59';

  echo '<table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow overflow-hidden">';
  echo '<thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2 text-left">Técnico</th>
      <th class="px-4 py-2 text-left">Servicios</th>
      <th class="px-4 py-2 text-left">Total</th>
      <th class="px-4 py-2 text-left">Estado</th>
      <th class="px-4 py-2 text-left">Acción</th>
    </tr>
  </thead><tbody class="divide-y divide-gray-100 text-sm">';

  foreach ($tecnicos as $idc) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN pago_generado = 1 THEN 1 ELSE 0 END) as pagados FROM servicios_omnipos WHERE idc = ? AND resultado IN ('Exito', 'Rechazo') AND fecha_atencion BETWEEN ? AND ?");
    $stmt->execute([$idc, $desde, $hasta]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];
    $pagados = $row['pagados'];

    $stmt2 = $pdo->prepare("SELECT SUM(p.monto_pago) FROM servicios_omnipos s JOIN precios_idc p ON s.idc = p.idc AND s.servicio = p.tipo_servicio AND s.resultado = p.resultado WHERE s.idc = ? AND s.resultado IN ('Exito', 'Rechazo') AND s.fecha_atencion BETWEEN ? AND ?");
    $stmt2->execute([$idc, $desde, $hasta]);
    $monto = $stmt2->fetchColumn() ?: 0;

    echo '<tr>';
    echo "<td class='px-4 py-2'>$idc</td>";
    echo "<td class='px-4 py-2'>$total</td>";
    echo "<td class='px-4 py-2'>\$" . number_format($monto, 2) . "</td>";
    echo "<td class='px-4 py-2'>" . ($pagados >= $total && $total > 0 ? 'Pagado' : 'Pendiente') . "</td>";
    echo '<td class="px-4 py-2">';
    if ($pagados < $total) {
      echo '<form method="POST" class="space-y-1">';
      echo "<input type='hidden' name='idc' value='$idc'>";
      echo "<input type='hidden' name='desde' value='{$_GET['desde']}'>";
      echo "<input type='hidden' name='hasta' value='{$_GET['hasta']}'>";
      echo "<input type='text' name='observaciones' placeholder='Observaciones' class='border rounded px-2 py-1 w-full'>";
      echo "<input type='text' name='folio' placeholder='Folio de pago' class='border rounded px-2 py-1 w-full'>";
      echo "<button type='submit' class='bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700'>💳 Marcar como Pagado</button>";
      echo '</form>';
    } else {
      echo '<span class="text-green-700 font-semibold">✔</span>';
    }
    echo '</td></tr>';
  }
  echo '</tbody></table>';
}
?>
