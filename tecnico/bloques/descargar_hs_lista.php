<?php if (empty($archivos)): ?>
  <div class="text-gray-500 text-center mt-8">
    No tienes archivos disponibles en este momento.
  </div>
<?php else: ?>
  <div class="space-y-4">
    <?php foreach ($archivos as $a): ?>
      <div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
        <div>
          <p class="font-medium"><?= htmlspecialchars($a['nombre']) ?></p>
          <p class="text-sm text-gray-500">Subido: <?= $a['fecha'] ?></p>
        </div>
        <a href="<?= $a['ruta_relativa'] ?>" target="_blank" class="text-blue-600 hover:underline text-sm">📥 Descargar</a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
