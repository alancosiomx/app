<a href="mis_servicios.php" class="inline-block text-blue-600 underline mb-4">← Regresar</a>

<h2 class="text-lg font-bold mb-2">📝 Detalles del Servicio</h2>

<div class="bg-white shadow rounded-xl p-4 text-sm space-y-2">
  <p><strong>🎫 Ticket:</strong> <?= htmlspecialchars($servicio['ticket']) ?></p>
  <p><strong>🏪 Comercio:</strong> <?= htmlspecialchars($servicio['comercio']) ?></p>
  <p><strong>📍 Domicilio:</strong> <?= htmlspecialchars($servicio['domicilio']) ?>, <?= htmlspecialchars($servicio['colonia']) ?>, <?= htmlspecialchars($servicio['ciudad']) ?>, CP <?= htmlspecialchars($servicio['cp']) ?></p>
  <p><strong>📞 Teléfono:</strong> <?= htmlspecialchars($servicio['telefono_contacto_1']) ?></p>
  <p><strong>🧾 Afiliación:</strong> <?= htmlspecialchars($servicio['afiliacion']) ?></p>
  <p><strong>🛠 Servicio:</strong> <?= htmlspecialchars($servicio['servicio']) ?></p>
  <p><strong>💬 Comentarios:</strong> <?= nl2br(htmlspecialchars($servicio['comentarios'])) ?></p>
  <hr class="my-6 border-t">

<h2 class="text-lg font-semibold mb-2">🕘 Historial de servicios anteriores</h2>

<?php if (empty($historial)): ?>
  <p class="text-sm text-gray-500">No hay servicios concluidos para esta afiliación.</p>
<?php else: ?>
  <div class="space-y-3">
    <?php foreach ($historial as $h): ?>
      <div class="bg-gray-50 p-3 rounded-xl shadow-sm text-sm text-gray-700">
        <p>📅 Fecha: <strong><?= date("d M Y", strtotime($h['fecha_atencion'])) ?></strong></p>
        <p>📞 Teléfono: <?= $h['telefono_contacto_1'] ?: '<span class="italic text-gray-400">Sin número</span>' ?></p>
        <p>🕒 Horario: <?= $h['horario'] ?: '<span class="italic text-gray-400">Sin horario</span>' ?></p>
        <p>💬 Comentarios: <?= nl2br(htmlspecialchars($h['comentarios'] ?? '')) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


</div>
