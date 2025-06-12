<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios por asignar
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos activos
// Obtener técnicos activos con rol "idc"
$tecnicos = $pdo->query("
    SELECT u.id, u.nombre
    FROM usuarios u
    JOIN usuarios_roles ur ON ur.usuario_id = u.id
    WHERE ur.rol = 'idc' AND u.activo = 1
")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h3 class="mt-3">Servicios Por Asignar</h3>

<form method="post" action="asignar_tecnico.php">
  <div class="mb-3">
    <label for="tecnico_id" class="form-label">Asignar al Técnico:</label>
    <select name="tecnico_id" id="tecnico_id" class="form-select" required>
      <option value="">Selecciona un técnico</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-sm table-hover" id="tabla_por_asignar">
      <thead class="table-light">
  <tr>
    <th><input type="checkbox" id="checkAll"></th>
    <th>Ticket</th>
    <th>Afiliación</th>
    <th>Servicio</th>
    <th>Comercio</th>
    <th>Ciudad</th>
    <th>CP</th>
    <th>Tipo TPV</th>
    <th>Modelo</th>
    <th>Teléfono</th>
    <th>Fecha Límite</th>
    <th>IDC</th>
    <th>Comentarios</th>
    <th>🔍</th>
  </tr>
</thead>
<tbody>
  <?php foreach ($servicios as $s): ?>
    <tr>
      <td><input type="checkbox" name="tickets[]" value="<?= $s['ticket'] ?>"></td>
      <td><?= htmlspecialchars($s['ticket']) ?></td>
      <td><?= htmlspecialchars($s['afiliacion']) ?></td>
      <td><?= htmlspecialchars($s['servicio']) ?></td>
      <td><?= htmlspecialchars($s['comercio']) ?></td>
      <td><?= htmlspecialchars($s['ciudad']) ?></td>
      <td><?= htmlspecialchars($s['cp']) ?></td>
      <td><?= htmlspecialchars($s['tipo_tpv']) ?></td>
      <td><?= htmlspecialchars($s['modelo']) ?></td>
      <td><?= htmlspecialchars($s['telefono_contacto_1']) ?></td>
      <td><?= htmlspecialchars($s['fecha_limite']) ?></td>
      <td><?= htmlspecialchars($s['idc']) ?></td>
      <td><?= htmlspecialchars($s['comentarios']) ?></td>
      <td><a href="#" class="ver-detalle" data-ticket="<?= $s['ticket'] ?>">🔍</a></td>
    </tr>
  <?php endforeach; ?>
</tbody>

  </div>

  <button type="submit" class="btn btn-primary mt-3">Asignar Servicios</button>
</form>

<script>
document.getElementById('checkAll').addEventListener('click', function () {
  const checkboxes = document.querySelectorAll('input[name="tickets[]"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
