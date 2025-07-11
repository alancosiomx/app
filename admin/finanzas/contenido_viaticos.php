<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/constants.php';
$usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';

$current_tab = $_GET['vista'] ?? 'viaticos';

// Guardar viático
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idc'], $_POST['ticket'], $_POST['monto'])) {
  $idc = $_POST['idc'];
  $ticket = $_POST['ticket'];
  $monto = $_POST['monto'];
  $comentarios = $_POST['comentarios'] ?? '';
  $motivo = 'Sin motivo';

  // Verifica si ya existe viático para ese ticket
  $existe = $pdo->prepare("SELECT 1 FROM viaticos WHERE ticket = ?");
  $existe->execute([$ticket]);
  if ($existe->fetch()) {
    echo "<div class='bg-yellow-100 text-yellow-800 p-4 mb-4 rounded'>⚠️ Ya existe un viático registrado para este ticket.</div>";
  } else {
    // Obtener datos del servicio
    $stmt = $pdo->prepare("SELECT afiliacion, colonia, ciudad FROM servicios_omnipos WHERE ticket = ? LIMIT 1");
    $stmt->execute([$ticket]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($servicio) {
      $insert = $pdo->prepare("INSERT INTO viaticos (idc, ticket, monto, afiliacion, poblacion, colonia, ciudad, comentarios, motivo, estado, fecha_registro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente', NOW())");

      $insert->execute([
        $idc,
        $ticket,
        $monto,
        $servicio['afiliacion'],
        $servicio['ciudad'], // poblacion
        $servicio['colonia'],
        $servicio['ciudad'],
        $comentarios,
        $motivo
      ]);

      echo "<div class='bg-green-100 text-green-800 p-4 mb-4 rounded'>✅ Viático registrado exitosamente para $ticket por \$$monto</div>";

      // Verificar si hubo viático anterior
      $verificar = $pdo->prepare("SELECT monto, fecha_registro FROM viaticos WHERE afiliacion = ? AND idc = ? ORDER BY fecha_registro DESC LIMIT 1 OFFSET 1");
      $verificar->execute([$servicio['afiliacion'], $idc]);
      $anterior = $verificar->fetch(PDO::FETCH_ASSOC);

      if ($anterior) {
        echo "<div class='bg-yellow-100 text-yellow-800 p-4 mb-4 rounded'>⚠️ Ya se visitó esta afiliación ({$servicio['afiliacion']}) por \${$anterior['monto']} el " . date('d/m/Y', strtotime($anterior['fecha_registro'])) . ".</div>";
      }
    } else {
      echo "<div class='bg-red-100 text-red-800 p-4 mb-4 rounded'>❌ Ticket no válido.</div>";
    }
  }
}

$tecnicos = $pdo->query("SELECT DISTINCT idc FROM servicios_omnipos WHERE idc IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="mb-4 border-b border-gray-200">
  <nav class="flex flex-wrap gap-2 text-sm font-medium text-gray-500" aria-label="Tabs">
    <?php foreach (TABS_FINANZAS as $clave => $titulo): ?>
      <a href="?vista=<?= $clave ?>"
         class="px-4 py-2 rounded-xl <?= $current_tab === $clave ? 'bg-blue-600 text-white' : 'hover:text-blue-700 bg-gray-100' ?>">
        <?= $titulo ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<h1 class="text-xl font-bold mb-4 text-blue-700">🧾 Registro de Viáticos</h1>

<form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div>
    <label class="block text-sm font-medium text-gray-600">Técnico</label>
    <select name="idc" id="idc-select" class="w-full border-gray-300 rounded-md">
      <option value="">Selecciona un técnico</option>
      <?php foreach ($tecnicos as $id): ?>
        <option value="<?= $id ?>"><?= $id ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Ticket</label>
    <select name="ticket" id="ticket-select" class="w-full border-gray-300 rounded-md" disabled>
      <option value="">Selecciona un técnico primero</option>
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-600">Monto</label>
    <input type="number" step="0.01" name="monto" class="w-full border-gray-300 rounded-md" required>
  </div>

  <div class="md:col-span-3">
    <label class="block text-sm font-medium text-gray-600">Comentarios</label>
    <textarea name="comentarios" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
  </div>

  <div class="md:col-span-3">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar Viático</button>
  </div>
</form>

<h2 class="text-lg font-semibold mb-2">Últimos Viáticos</h2>
<table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow overflow-hidden">
  <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
    <tr>
      <th class="px-4 py-2">Técnico</th>
      <th class="px-4 py-2">Ticket</th>
      <th class="px-4 py-2">Afiliación</th>
      <th class="px-4 py-2">Población</th>
      <th class="px-4 py-2">Colonia</th>
      <th class="px-4 py-2">Ciudad</th>
      <th class="px-4 py-2">Monto</th>
      <th class="px-4 py-2">Fecha</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-100 text-sm">
    <?php
    $ultimos = $pdo->query("SELECT * FROM viaticos ORDER BY fecha_registro DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($ultimos as $v): ?>
      <tr>
        <td class="px-4 py-2"><?= htmlspecialchars($v['idc']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['ticket'] ?? '—') ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['afiliacion']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['poblacion'] ?? '—') ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['colonia']) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($v['ciudad']) ?></td>
        <td class="px-4 py-2">$<?= number_format($v['monto'], 2) ?></td>
        <td class="px-4 py-2"><?= date('d/m/Y', strtotime($v['fecha_registro'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
  document.getElementById('idc-select').addEventListener('change', function () {
    const tecnico = this.value;
    const ticketSelect = document.getElementById('ticket-select');

    ticketSelect.innerHTML = '<option value="">Cargando...</option>';
    ticketSelect.disabled = true;

    if (tecnico) {
      fetch('fetch_tickets.php?idc=' + encodeURIComponent(tecnico))
        .then(response => response.json())
        .then(data => {
          ticketSelect.innerHTML = '';
          if (data.length > 0) {
            data.forEach(ticket => {
              const option = document.createElement('option');
              option.value = ticket;
              option.textContent = ticket;
              ticketSelect.appendChild(option);
            });
            ticketSelect.disabled = false;
          } else {
            ticketSelect.innerHTML = '<option value="">Sin tickets asignados</option>';
            ticketSelect.disabled = true;
          }
        });
    } else {
      ticketSelect.innerHTML = '<option value="">Selecciona un técnico primero</option>';
      ticketSelect.disabled = true;
    }
  });
</script>
