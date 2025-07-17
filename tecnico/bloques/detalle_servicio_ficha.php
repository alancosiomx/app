<a href="mis_servicios.php" class="inline-block text-blue-600 underline mb-4">← Regresar</a>

<h2 class="text-lg font-bold mb-2">📝 Detalles del Servicio</h2>

<div class="bg-white shadow rounded-xl p-4 text-sm space-y-2">
  <p><strong>🎫 Ticket:</strong> <?= htmlspecialchars($servicio['ticket']) ?></p>
  <p><strong>🏪 Comercio:</strong> <?= htmlspecialchars($servicio['comercio']) ?></p>
  <p><strong>📍 Domicilio:</strong> <?= htmlspecialchars($servicio['domicilio']) ?>, <?= htmlspecialchars($servicio['colonia']) ?>, <?= htmlspecialchars($servicio['ciudad']) ?>, CP <?= htmlspecialchars($servicio['cp']) ?></p>
  <p><strong>📞 Teléfono:</strong> <?= htmlspecialchars($servicio['telefono_contacto_1']) ?></p>
  <p><strong>🧾 Afiliación:</strong> <?= htmlspecialchars($servicio['afiliacion']) ?></p>
  <p><strong>🛠 Servicio:</strong> <?= htmlspecialchars($servicio['servicio']) ?></p>
  <p><strong>🕒 Hora Atención:</strong> <?= htmlspecialchars($servicio['hora_atencion']) ?></p>
  <p><strong>💬 Comentarios:</strong> <?= nl2br(htmlspecialchars($servicio['comentarios'])) ?></p>

  <?php if (!empty($servicio['serie_instalada'])): ?>
    <p><strong>📦 Serie Instalada:</strong> <?= htmlspecialchars($servicio['serie_instalada']) ?></p>
  <?php endif; ?>

  <?php if (!empty($servicio['serie_retiro'])): ?>
    <p><strong>📤 Serie Retiro:</strong> <?= htmlspecialchars($servicio['serie_retiro']) ?></p>
  <?php endif; ?>
</div>
