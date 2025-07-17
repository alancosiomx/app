<?php if (empty($servicios)): ?>
  <div class="text-gray-500 text-center mt-8">
    No tienes servicios en ruta asignados.
  </div>
<?php else: ?>
  <div class="space-y-4">
    <?php foreach ($servicios as $serv): ?>
      <?php
        $texto_vim = strtolower($serv['vim'] ?? '');

        $es_vim = str_contains($texto_vim, '24 horas') || str_contains($texto_vim, '4 horas');
        $es_premium = str_contains($texto_vim, 'premium');
        $tiene_cita = !empty($serv['fecha_cita']);

        $etiquetas = [];
        if ($es_vim) $etiquetas[] = '<span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded-full font-medium">⚡ VIM</span>';
        if ($es_premium) $etiquetas[] = '<span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full font-medium">💎 Premium</span>';
        if ($tiene_cita) {
          $fecha_formateada = date('d M Y', strtotime($serv['fecha_cita']));
          $etiquetas[] = "<span class='bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-medium'>📅 $fecha_formateada</span>";
        }
      ?>
      <div class="bg-white shadow rounded-xl p-4 space-y-2">
        <div class="flex justify-between items-center">
          <div class="font-semibold text-lg"><?= htmlspecialchars($serv['comercio']) ?></div>
        </div>

        <?php if (!empty($etiquetas)): ?>
          <div class="flex flex-wrap gap-2">
            <?= implode(' ', $etiquetas) ?>
          </div>
        <?php endif; ?>

        <div class="text-sm text-gray-600 space-y-1 mt-2">
          <div>🧾 Ticket: <strong><?= $serv['ticket'] ?></strong></div>
          <div>🔢 Afiliación: <?= $serv['afiliacion'] ?></div>
          <div>🏙️ Ciudad: <?= $serv['ciudad'] ?></div>
          <div>🔧 Tipo: <?= $serv['servicio'] ?? '<span class="text-gray-400 italic">No definido</span>' ?></div>
          <div>📞 Tel: <?= $serv['telefono_contacto_1'] ?? '<span class="text-gray-400 italic">Sin número</span>' ?></div>
        </div>

        <div class="mt-2">
          <a href="detalle_servicio.php?ticket=<?= urlencode($serv['ticket']) ?>" class="text-blue-600 text-sm hover:underline">🔍 Ver detalle</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
