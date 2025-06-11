<?php
require_once '../../config.php';

// Obtener servicios por asignar
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener técnicos activos
$tecnicos = $pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 AND roles LIKE '%idc%'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>Servicios Por Asignar</h3>

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

  <table class="table table-bordered table-sm table-hover" id="tabla_por_asignar">
    <thead class="table-light">
      <tr>
        <th><input type="checkbox" id="checkAll"></th>
        <th>Ticket</th>
        <th>Afiliación</th>
        <th>Comercio</th>
        <th>Ciudad</th>
        <th>CP</th>
        <th>Fecha Límite</th>
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
          <td><?= htmlspecialchars($s['comercio']) ?></td>
          <td><?= htmlspecialchars($s['ciudad']) ?></td>
          <td><?= htmlspecialchars($s['cp']) ?></td>
          <td><?= htmlspecialchars($s['fecha_limite']) ?></td>
          <td><?= htmlspecialchars($s['comentarios']) ?></td>
          <td><a href="#" class="ver-detalle" data-ticket="<?= $s['ticket'] ?>">🔍</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <button type="submit" class="btn btn-primary mt-3">Asignar Servicios</button>
</form>

<script>
document.getElementById('checkAll').addEventListener('click', function () {
  const checkboxes = document.querySelectorAll('input[name="tickets[]"]');
  for (const cb of checkboxes) {
    cb.checked = this.checked;
  }
});
</script>
