<?php
// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- 🔍 FORMULARIO DE FILTRO -->
<div class="bg-white p-4 rounded shadow mb-6">
  <form method="GET" action="">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="text-sm font-medium">Desde</label>
        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>" class="w-full border rounded px-2 py-1">
      </div>
      <div>
        <label class="text-sm font-medium">Hasta</label>
        <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>" class="w-full border rounded px-2 py-1">
      </div>
      <div>
        <label class="text-sm font-medium">Técnico (opcional)</label>
        <input type="text" name="tecnico" value="<?= htmlspecialchars($_GET['tecnico'] ?? '') ?>" class="w-full border rounded px-2 py-1">
      </div>
      <div class="flex items-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
          Filtrar
        </button>
      </div>
    </div>
  </form>
</div>

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

  if (count($servicios) > 0) {
?>

<!-- ✅ BOTÓN EXPORTAR A EXCEL -->
<form method="POST" action="exportar_cobros_excel.php" target="_blank" class="mb-4">
  <input type="hidden" name="desde" value="<?= htmlspecialchars($_GET['desde']) ?>">
  <input type="hidden" name="hasta" value="<?= htmlspecialchars($_GET['hasta']) ?>">
  <input type="hidden" name="tecnico" value="<?= htmlspecialchars($_GET['tecnico'] ?? '') ?>">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
    📥 Exportar a Excel
  </button>
</form>

<!-- 🔽 TABLA DE SERVICIOS -->
<table class="min-w-full divide-y divide-gray-200 mt-6 bg-white rounded-xl shadow overflow-hidden">
  <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2 text-left">Ticket</th>
      <th class="px-4 py-2 text-left">Comercio</th>
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
    <?php foreach ($servicios as $serv) {
      $fecha_atencion = $serv['fecha_atencion'] ?? null;
      $fecha_limite = $serv['fecha_limite'] ?? null;
      $sla = ($fecha_atencion && $fecha_limite && strtotime($fecha_atencion) <= strtotime($fecha_limite)) ? 'DT' : 'FT';
      $pagado = $serv['pago_generado'] ?? 0;
      $ticket = $serv['ticket'];

      $rechazo_stmt = $pdo->prepare("SELECT fecha_visita FROM visitas_servicios WHERE ticket = ? AND resultado = 'Rechazo' LIMIT 1");
      $rechazo_stmt->execute([$ticket]);
      $rechazo_info = $rechazo_stmt->fetchColumn();

      $cita_stmt = $pdo->prepare("SELECT fecha_visita FROM visitas_servicios WHERE ticket = ? AND tipo_visita = 'Cita' ORDER BY fecha_visita DESC LIMIT 1");
      $cita_stmt->execute([$ticket]);
      $cita_fecha = $cita_stmt->fetchColumn();
    ?>
    <tr>
      <td class="px-4 py-2 font-mono text-blue-700"><?= htmlspecialchars($ticket) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($serv['comercio']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($serv['idc']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($serv['servicio']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($serv['resultado']) ?></td>
      <td class="px-4 py-2"><?= $sla ?></td>
      <td class="px-4 py-2"><?= $serv['fecha_atencion'] ?></td>
      <td class="px-4 py-2">$<?= number_format(calcular_pago($pdo, $serv), 2) ?></td>
      <td class="px-4 py-2">
        <?php if ($pagado) { ?>
          <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Pagado</span>
        <?php } else { ?>
          <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendiente</span>
        <?php } ?>

        <?php if ($rechazo_info) { ?>
          <div class="text-[10px] text-gray-500 italic mt-1">Rechazo previo el <?= date('Y-m-d', strtotime($rechazo_info)) ?></div>
        <?php } ?>

        <?php if ($cita_fecha) { ?>
          <div class="text-[10px] text-blue-500 italic mt-1">Cita realizada el <?= date('Y-m-d', strtotime($cita_fecha)) ?></div>
        <?php } ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php
  } else {
    echo '<div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4">No se encontraron servicios para ese rango.</div>';
  }
} else {
  echo '<div class="text-gray-500 italic">Usa el formulario para generar un reporte de cobros.</div>';
}
?>
