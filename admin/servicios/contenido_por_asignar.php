<?php
require_once __DIR__ . '/../../config.php';

// Filtros
$ciudad = $_GET['ciudad'] ?? '';
$servicio = $_GET['servicio'] ?? '';
$buscar = $_GET['buscar'] ?? '';

// Query base
$sql = "SELECT * FROM servicios_omnipos WHERE estatus = 'Por Asignar'";
$params = [];

if ($ciudad !== '') {
    $sql .= " AND ciudad = ?";
    $params[] = $ciudad;
}

if ($servicio !== '') {
    $sql .= " AND servicio = ?";
    $params[] = $servicio;
}

if ($buscar !== '') {
    $busquedas = array_filter(preg_split('/[\s,]+/', $buscar));
    $filtros = [];
    foreach ($busquedas as $b) {
        $filtros[] = "(ticket LIKE ? OR afiliacion LIKE ? OR comercio LIKE ?)";
        $params[] = "%$b%";
        $params[] = "%$b%";
        $params[] = "%$b%";
    }
    $sql .= " AND (" . implode(" OR ", $filtros) . ")";
}

$sql .= " ORDER BY fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Técnicos activos
$tecnicos = $pdo->query("
    SELECT u.id, u.nombre
    FROM usuarios u
    JOIN usuarios_roles r ON r.usuario_id = u.id
    WHERE r.rol = 'idc' AND u.activo = 1
")->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$ciudades = $pdo->query("SELECT DISTINCT ciudad FROM servicios_omnipos WHERE estatus = 'Por Asignar' AND ciudad IS NOT NULL AND ciudad != '' ORDER BY ciudad")->fetchAll(PDO::FETCH_COLUMN);
$serviciosUnicos = $pdo->query("SELECT DISTINCT servicio FROM servicios_omnipos WHERE estatus = 'Por Asignar' AND servicio IS NOT NULL AND servicio != '' ORDER BY servicio")->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<!-- Filtros -->
<form method="get" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
  <input type="hidden" name="tab" value="por_asignar">

  <div>
    <label class="text-sm font-medium">Ciudad</label>
    <select name="ciudad" class="w-full border rounded px-3 py-1 text-sm">
      <option value="">Todas</option>
      <?php foreach ($ciudades as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>" <?= $ciudad === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="text-sm font-medium">Servicio</label>
    <select name="servicio" class="w-full border rounded px-3 py-1 text-sm">
      <option value="">Todos</option>
      <?php foreach ($serviciosUnicos as $s): ?>
        <option value="<?= htmlspecialchars($s) ?>" <?= $servicio === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="text-sm font-medium">Buscar</label>
    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Ticket, Afiliación, Comercio" class="w-full border rounded px-3 py-1 text-sm">
  </div>

  <div class="flex items-end">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm w-full">Filtrar</button>
  </div>
</form>

<!-- Asignación múltiple -->
<form action="asignar_tecnico.php" method="post">
  <div class="flex justify-between items-center mb-3">
    <div class="flex items-center gap-2">
      <label for="tecnico_id" class="text-sm font-medium">Asignar a:</label>
      <select name="tecnico_id" id="tecnico_id" class="border rounded px-3 py-1 text-sm" required>
        <option value="">Selecciona un técnico</option>
        <?php foreach ($tecnicos as $t): ?>
          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">
      Asignar Seleccionados
    </button>
  </div>

  <div class="overflow-x-auto">
    <table id="tabla-servicios" class="min-w-full table-auto bg-white shadow rounded-lg">
      <thead class="bg-gray-100 text-gray-700 text-sm">
  <tr>
    <th class="px-4 py-2"><input type="checkbox" id="checkAll" onclick="toggleAll(this)"></th>
    <th class="px-4 py-2">Banco</th>
    <th class="px-4 py-2">Ticket</th>
    <th class="px-4 py-2">Extras</th>
    <th class="px-4 py-2">Afiliación</th>
    <th class="px-4 py-2">Comercio</th>
    <th class="px-4 py-2">Ciudad</th>
    <th class="px-4 py-2">Servicio</th>
    <th class="px-4 py-2">Horario</th>
    <th class="px-4 py-2">Fecha Inicio</th>
    <th class="px-4 py-2">Fecha Límite</th>
    <th class="px-4 py-2">VIM</th>
    <th class="px-4 py-2">Comentarios</th>
    <th class="px-4 py-2">Insumos</th>
    <th class="px-4 py-2 text-center">🔍</th>
  </tr>
</thead>

      <tbody>
  <?php foreach ($servicios as $s): ?>
    <?php
      $extras = '';
      $vim = (string)($s['vim'] ?? '');

      if (stripos($vim, '4 horas') !== false || stripos($vim, '24 horas') !== false) {
          $extras .= '⚡';
      }

      if (stripos($vim, 'premium') !== false) {
          $extras .= '💎';
      }

      if (!empty($s['fecha_cita'])) {
          $extras .= ' 📅';
      }
    ?>
    <tr class="border-t text-sm hover:bg-gray-50">
      <td class="px-4 py-2 text-center">
        <input type="checkbox" name="tickets[]" value="<?= htmlspecialchars($s['ticket'] ?? '') ?>">
      </td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['banco'] ?? '') ?></td>
      <td class="px-4 py-2 font-medium text-blue-600"><?= htmlspecialchars($s['ticket'] ?? '') ?></td>
      <td class="px-4 py-2"><?= $extras ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['afiliacion'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['comercio'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['ciudad'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['servicio'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['horario'] ?? '') ?></td>
      <td class="px-4 py-2 text-xs"><?= htmlspecialchars($s['fecha_inicio'] ?? '') ?></td>
      <td class="px-4 py-2 text-xs"><?= htmlspecialchars($s['fecha_limite'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['vim'] ?? '') ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['cantidad_insumos'] ?? '') ?></td>
      <td class="px-4 py-2 text-xs text-gray-500 whitespace-pre-line"><?= nl2br(htmlspecialchars($s['comentarios'] ?? '—')) ?></td>
      <td class="px-4 py-2 text-center">
        <a href="#" class="ver-detalle" data-ticket="<?= htmlspecialchars($s['ticket']) ?>">🔍</a>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>


    </table>
  </div>
</form>

<!-- DataTables + scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
function toggleAll(source) {
  document.querySelectorAll('input[name="tickets[]"]').forEach(cb => cb.checked = source.checked);
}

document.addEventListener('DOMContentLoaded', function () {
  $('#tabla-servicios').DataTable({
    pageLength: 100,
    order: [[1, 'desc']],
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron coincidencias",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      paginate: {
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
});
    <script>
document.addEventListener('DOMContentLoaded', function () {
  $('#tabla-servicios').DataTable({
    pageLength: 100,
    order: [[1, 'desc']],
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron coincidencias",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      paginate: {
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
    <script>
function cerrarModal() {
  const modal = document.getElementById('modalDetalleServicio');
  if (modal) modal.remove();
}

document.addEventListener('DOMContentLoaded', function () {
  document.body.addEventListener('click', function (e) {
    const link = e.target.closest('.ver-detalle');
    if (!link) return;

    e.preventDefault();
    const ticket = (link.dataset.ticket || '').trim();
    if (!ticket) return alert('Ticket no válido');

    fetch('detalle_servicio.php?ticket=' + encodeURIComponent(ticket))
      .then(res => res.text())
      .then(html => {
        const existente = document.getElementById('modalDetalleServicio');
        if (existente) existente.remove();

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.body.appendChild(wrapper.firstElementChild);
      })
      .catch(err => {
        console.error('Error AJAX:', err);
        alert('No se pudo cargar el detalle.');
      });
  });
});
</script>


</script>


