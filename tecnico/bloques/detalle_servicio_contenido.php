<div class="space-y-6">

  <!-- Servicio actual -->
  <div class="bg-white shadow rounded-xl p-4">
    <h2 class="text-lg font-bold mb-2"><?= htmlspecialchars($servicio['comercio']) ?></h2>
    <div class="text-sm text-gray-600 space-y-1">
      <p>🧾 Ticket: <?= $servicio['ticket'] ?></p>
      <p>🔢 Afiliación: <?= $servicio['afiliacion'] ?></p>
      <p>🏙️ Ciudad: <?= $servicio['ciudad'] ?></p>
      <p>🏦 Banco: <?= $servicio['banco'] ?? 'N/D' ?></p>
      <p>🔧 Tipo: <?= $servicio['servicio'] ?? 'N/D' ?></p>
      <p>📞 Teléfono: <?= $servicio['telefono_contacto_1'] ?? 'N/D' ?></p>
      <p>📍 Dirección: <?= $servicio['domicilio'] ?? 'N/D' ?></p>
      <p>📅 Fecha límite: <?= $servicio['fecha_limite'] ?? 'N/D' ?></p>
    </div>

    <a href="?ticket=<?= urlencode($servicio['ticket']) ?>&ver_historial=1"
       class="inline-block mt-4 text-sm text-blue-600 hover:underline">
       🕘 Ver historial de esta afiliación
    </a>
  </div>

  <!-- Historial si se solicita -->
  <?php if (!empty($_GET['ver_historial'])): ?>
    <hr class="my-6 border-t">
    <h3 class="text-base font-semibold mb-2">🕘 Historial de servicios concluidos</h3>

    <?php
      $stmt_hist = $pdo->prepare("
          SELECT fecha_atencion, telefono_contacto_1, comentarios, horario
          FROM servicios_omnipos
          WHERE afiliacion = ? AND estatus = 'Concluido' AND ticket != ?
          ORDER BY fecha_atencion DESC
      ");
      $stmt_hist->execute([$servicio['afiliacion'], $servicio['ticket']]);
      $historial = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (empty($historial)): ?>
      <p class="text-sm text-gray-500">No hay servicios concluidos para esta afiliación.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($historial as $h): ?>
          <div class="bg-gray-50 p-3 rounded-xl shadow-sm text-sm text-gray-700">
            <p>📅 Fecha atención: <strong><?= date("d M Y", strtotime($h['fecha_atencion'])) ?></strong></p>
            <p>📞 Teléfono: <?= $h['telefono_contacto_1'] ?: '<span class="italic text-gray-400">Sin número</span>' ?></p>
            <p>🕒 Horario: <?= $h['horario'] ?: '<span class="italic text-gray-400">Sin horario</span>' ?></p>
            <p>💬 Comentarios: <?= nl2br(htmlspecialchars($h['comentarios'] ?? '')) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>
