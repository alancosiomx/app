<?php
require_once __DIR__ . '/../../config.php';

// Obtener servicios en ruta
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE estatus = 'En Ruta' ORDER BY fecha_inicio DESC");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<p class="text-gray-700 text-sm mb-4">Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></strong></p>

<?php include __DIR__ . '/../includes/tabs_servicios.php'; ?>

<h2 class="text-xl font-semibold text-gray-800 mb-4">🛠 Servicios En Ruta</h2>

<div class="overflow-x-auto bg-white shadow rounded-xl">
  <table class="min-w-full text-sm text-left text-gray-700">
    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
      <tr>
        <th class="px-4 py-3">Ticket</th>
        <th class="px-4 py-3">Afiliación</th>
        <th class="px-4 py-3">Comercio</th>
        <th class="px-4 py-3">Ciudad</th>
        <th class="px-4 py-3">Servicio</th>
        <th class="px-4 py-3">Comentarios</th>
        <th class="px-4 py-3 text-center">🔍</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
  <?php foreach ($servicios as $s): ?>
    <tr class="hover:bg-gray-50">
      <td class="px-4 py-2"><?= htmlspecialchars($s['ticket']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['afiliacion']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['comercio']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['ciudad']) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($s['servicio']) ?></td>
      <td class="px-4 py-2 whitespace-pre-line"><?= nl2br(htmlspecialchars($s['comentarios'])) ?></td>
      <td class="px-4 py-2 text-center">
        <a href="#" class="ver-detalle text-blue-600 hover:underline mb-2 block" data-ticket="<?= $s['ticket'] ?>">🔍</a>

        <!-- Formulario para concluir -->
        <form method="POST" action="acciones/cerrar_servicio.php" class="space-y-1">
          <input type="hidden" name="ticket" value="<?= htmlspecialchars($s['ticket']) ?>">

          <select name="resultado" required class="w-full text-sm px-2 py-1 border border-gray-300 rounded">
            <option value="">-- Resultado --</option>
            <option value="Éxito">✅ Éxito</option>
            <option value="Rechazo">❌ Rechazo</option>
            <option value="Reasignar">🔁 Reasignar</option>
          </select>

          <button type="submit" class="w-full mt-1 bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700">Guardar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>

  </table>
</div>
