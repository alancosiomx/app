<?php
require_once __DIR__ . '/../../config.php';

$ticket = $_GET['ticket'] ?? '';

if (!$ticket) {
    echo "<div class='alert alert-danger'>Ticket no válido.</div>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "<div class='alert alert-warning'>Servicio no encontrado.</div>";
    exit;
}
?>

<div class="modal fade" id="modalDetalleServicio" tabindex="-1" aria-labelledby="detalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="detalleLabel">Detalle del Servicio - <?= htmlspecialchars($servicio['ticket']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm">
          <?php foreach ($servicio as $campo => $valor): ?>
            <tr>
              <th><?= ucwords(str_replace("_", " ", $campo)) ?></th>
              <td><?= nl2br(htmlspecialchars($valor)) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</div>
