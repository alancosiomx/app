<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios en ruta
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'En Ruta' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h3>Servicios En Ruta</h3>

<div class="table-responsive">
  <table class="table table-bordered table-sm table-hover" id="tabla_en_ruta">
    <thead class="table-light">
      <tr>
        <th>Ticket</th>
        <th>Afiliación</th>
        <th>Comercio</th>
        <th>Ciudad</th>
        <th>Tipo de Servicio</th>
        <th>Comentarios</th>
        <th>🔍</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($servicios as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['ticket']) ?></td>
          <td><?= htmlspecialchars($s['afiliacion']) ?></td>
          <td><?= htmlspecialchars($s['comercio']) ?></td>
          <td><?= htmlspecialchars($s['ciudad']) ?></td>
          <td><?= htmlspecialchars($s['tipo_servicio']) ?></td>
          <td><?= nl2br(htmlspecialchars($s['comentarios'])) ?></td>
          <td><a href="#" class="ver-detalle" data-ticket="<?= $s['ticket'] ?>">🔍</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
