<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios en ruta
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'En Ruta' ORDER BY fecha_atencion DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Gestión de Servicios - OMNIPOS</h1>
<p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></strong></p>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $_GET['tab'] === 'por_asignar' ? 'active' : '' ?>" href="?tab=por_asignar">Por Asignar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $_GET['tab'] === 'en_ruta' ? 'active' : '' ?>" href="?tab=en_ruta">En Ruta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $_GET['tab'] === 'concluido' ? 'active' : '' ?>" href="?tab=concluido">Histórico</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $_GET['tab'] === 'citas' ? 'active' : '' ?>" href="?tab=citas">Citas</a>
    </li>
</ul>

<h3>Servicios En Ruta</h3>

<table class="table table-bordered table-sm table-hover" id="tabla_en_ruta">
  <thead class="table-light">
    <tr>
      <th>Ticket</th>
      <th>Afiliación</th>
      <th>Comercio</th>
      <th>Ciudad</th>
      <th>Fecha Atención</th>
      <th>Resultado</th>
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
        <td><?= htmlspecialchars($s['fecha_atencion']) ?></td>
        <td><?= htmlspecialchars($s['resultado'] ?? '—') ?></td>
        <td><a href="#" class="ver-detalle" data-ticket="<?= $s['ticket'] ?>">🔍</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
