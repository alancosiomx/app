<h2 class="text-xl font-bold mb-4">📋 Mis Servicios Asignados</h2>

<div class="overflow-x-auto">
  <table class="table-auto w-full border text-sm">
    <thead class="bg-gray-200">
      <tr>
        <th class="px-2 py-1 border">Ticket</th>
        <th class="px-2 py-1 border">Afiliación</th>
        <th class="px-2 py-1 border">Comercio</th>
        <th class="px-2 py-1 border">Ciudad</th>
        <th class="px-2 py-1 border">Fecha Atención</th>
        <th class="px-2 py-1 border">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($servicios as $servicio): ?>
        <tr>
          <td class="border px-2 py-1"><?= htmlspecialchars($servicio['ticket']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($servicio['afiliacion']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($servicio['comercio']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($servicio['ciudad']) ?></td>
          <td class="border px-2 py-1"><?= htmlspecialchars($servicio['fecha_atencion']) ?></td>
          <td class="border px-2 py-1">
            <a href="generar_hs.php?ticket=<?= urlencode($servicio['ticket']) ?>" 
               class="text-blue-600 underline text-xs" target="_blank">
              🧾 Generar HS
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
