<h2 class="text-2xl font-bold mb-4">🔔 Avisos del Administrador</h2>

<?php if (empty($alertas)): ?>
  <p class="text-gray-500">No tienes alertas nuevas.</p>
<?php else: ?>
  <ul class="space-y-4">
    <?php foreach ($alertas as $a): ?>
      <li class="p-4 rounded-lg shadow <?= $a['leida'] ? 'bg-gray-100' : 'bg-yellow-100' ?>">
        <p class="text-sm text-gray-600 mb-1"><?= date("d/m/Y H:i", strtotime($a['fecha_creacion'])) ?></p>
        <p class="text-gray-800"><?= nl2br(htmlspecialchars($a['mensaje'])) ?></p>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
