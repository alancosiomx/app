<?php if (count($servicios) === 0): ?>
  <p class="text-gray-500 text-center mt-10">No tienes servicios activos por ahora.</p>
<?php else: ?>
  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($servicios as $srv): ?>
      <div class="bg-white rounded-2xl shadow p-4 border border-gray-200 hover:shadow-md transition-all duration-300">
        <div class="text-sm text-gray-500 mb-1">Ticket</div>
        <div class="text-lg font-semibold text-blue-600"><?= htmlspecialchars($srv['ticket']) ?></div>

        <div class="mt-2">
          <div class="text-sm text-gray-500">Afiliación</div>
          <div class="font-medium"><?= htmlspecialchars($srv['afiliacion']) ?></div>
        </div>

        <div class="mt-2">
          <div class="text-sm text-gray-500">Comercio</div>
          <div class="font-medium"><?= htmlspecialchars($srv['comercio']) ?></div>
        </div>

        <div class="mt-2">
          <div class="text-sm text-gray-500">Ciudad</div>
          <div><?= htmlspecialchars($srv['ciudad']) ?></div>
        </div>

        <div class="mt-2">
          <div class="text-sm text-gray-500">Fecha At.</div>
          <div><?= htmlspecialchars($srv['fecha_atencion'] ?? '—') ?></div>
        </div>

        <div class="mt-4 text-right">
          <a href="cerrar_servicio.php?ticket=<?= urlencode($srv['ticket']) ?>" 
             class="inline-block bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-blue-700">
            Cerrar Servicio
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
