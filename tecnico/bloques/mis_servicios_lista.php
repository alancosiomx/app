<?php if (empty($servicios)): ?>
  <div class="text-gray-500 text-center mt-8">
    No tienes servicios en ruta asignados.
  </div>
<?php else: ?>
  <div class="space-y-4">
    <?php foreach ($servicios as $serv): ?>
      <div class="bg-white shadow rounded-xl p-4">
        <div class="font-semibold text-lg"><?= htmlspecialchars($serv['comercio']) ?></div>
        <div class="text-sm text-gray-500">
          Ticket: <strong><?= $serv['ticket'] ?></strong><br>
          Afiliación: <?= $serv['afiliacion'] ?><br>
          Ciudad: <?= $serv['ciudad'] ?>
        </div>
        <div class="mt-2">
          <a href="detalle_servicio.php?ticket=<?= urlencode($serv['ticket']) ?>" class="text-blue-600 text-sm hover:underline">🔍 Ver detalle</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
