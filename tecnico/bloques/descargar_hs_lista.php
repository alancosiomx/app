<div class="p-4 space-y-4">
  <h2 class="text-xl font-semibold mb-4">📥 Hojas de Servicio Generadas</h2>

  <?php if (empty($servicios)): ?>
    <p class="text-gray-500">Aún no hay servicios cerrados.</p>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-xl shadow text-sm">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-3">Ticket</th>
            <th class="p-3">Afiliación</th>
            <th class="p-3">Comercio</th>
            <th class="p-3">Fecha de cierre</th>
            <th class="p-3">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($servicios as $s): ?>
            <tr class="border-b">
              <td class="p-3"><?= htmlspecialchars($s['ticket']) ?></td>
              <td class="p-3"><?= htmlspecialchars($s['afiliacion']) ?></td>
              <td class="p-3"><?= htmlspecialchars($s['comercio']) ?></td>
              <td class="p-3"><?= date('d/m/Y', strtotime($s['fecha_cierre'])) ?></td>
              <td class="p-3">
                <a href="generar_hs.php?ticket=<?= urlencode($s['ticket']) ?>" target="_blank" class="text-blue-600 hover:underline">Descargar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
