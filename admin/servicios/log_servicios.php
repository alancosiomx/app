<?php
require_once '../../config.php';
session_start();

$filtro_ticket = $_GET['ticket'] ?? '';
$filtro_usuario = $_GET['usuario'] ?? '';

$sql = "SELECT * FROM log_servicios WHERE 1=1";
$params = [];

if ($filtro_ticket) {
    $sql .= " AND ticket LIKE ?";
    $params[] = "%$filtro_ticket%";
}

if ($filtro_usuario) {
    $sql .= " AND usuario LIKE ?";
    $params[] = "%$filtro_usuario%";
}

$sql .= " ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>Log de Servicios</h3>

<form class="row g-3 mb-3">
  <div class="col-auto">
    <input type="text" name="ticket" placeholder="Buscar por Ticket" class="form-control" value="<?= htmlspecialchars($filtro_ticket) ?>">
  </div>
  <div class="col-auto">
    <input type="text" name="usuario" placeholder="Buscar por Usuario" class="form-control" value="<?= htmlspecialchars($filtro_usuario) ?>">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-secondary">Buscar</button>
  </div>
</form>

<table class="table table-sm table-striped">
  <thead class="table-light">
    <tr>
      <th>#</th>
      <th>Ticket</th>
      <th>Acción</th>
      <th>Usuario</th>
      <th>Detalles</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= $log['id'] ?></td>
        <td><?= htmlspecialchars($log['ticket']) ?></td>
        <td><?= htmlspecialchars($log['accion']) ?></td>
        <td><?= htmlspecialchars($log['usuario']) ?></td>
        <td><?= nl2br(htmlspecialchars($log['detalles'])) ?></td>
        <td><?= $log['fecha'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
