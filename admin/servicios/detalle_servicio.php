<?php
require_once __DIR__ . '/../../config.php';

$ticket = trim($_GET['ticket'] ?? '');

if (!$ticket) {
    echo "<div class='text-red-600 p-4'>❌ Ticket no válido.</div>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE TRIM(ticket) = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "<div class='text-yellow-600 p-4'>⚠️ Servicio no encontrado.</div>";
    exit;
}

function mostrar($campo, $valor) {
    if (empty($valor) || strtoupper(trim($valor)) === 'NA') return '';
    $label = ucwords(str_replace("_", " ", $campo));
    return "
      <tr class='border-b'>
        <th class='bg-gray-100 px-4 py-2 font-semibold w-1/3 text-gray-700'>{$label}</th>
        <td class='px-4 py-2 whitespace-pre-line text-gray-800'>". nl2br(htmlspecialchars(trim((string)$valor))) ."</td>
      </tr>
    ";
}
?>

<div id="modalDetalleServicio" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 relative">
    <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl">✖</button>

    <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">
      🔍 Detalle del Servicio – <?= htmlspecialchars($servicio['ticket']) ?>
    </h2>

    <div class="overflow-y-auto max-h-[70vh] border rounded-lg">
      <table class="w-full text-sm text-left text-gray-700">
        <tbody>

          <tr><td colspan="2" class="text-xs font-bold text-gray-600 bg-gray-50 px-4 py-2">📄 Información General</td></tr>
          <?= mostrar('banco', $servicio['banco']) ?>
          <?= mostrar('fecha_inicio', $servicio['fecha_inicio']) ?>
          <?= mostrar('vim', $servicio['vim']) ?>
          <?= mostrar('afiliacion', $servicio['afiliacion']) ?>
          <?= mostrar('servicio', $servicio['servicio']) ?>

          <tr><td colspan="2" class="text-xs font-bold text-gray-600 bg-gray-50 px-4 py-2">📍 Ubicación</td></tr>
          <?= mostrar('comercio', $servicio['comercio']) ?>
          <?= mostrar('domicilio', $servicio['domicilio']) ?>
          <?= mostrar('colonia', $servicio['colonia']) ?>
          <?= mostrar('ciudad', $servicio['ciudad']) ?>
          <?= mostrar('cp', $servicio['cp']) ?>

          <tr><td colspan="2" class="text-xs font-bold text-gray-600 bg-gray-50 px-4 py-2">📞 Contacto</td></tr>
          <?= mostrar('solicito', $servicio['solicito']) ?>
          <?= mostrar('telefono_contacto_1', $servicio['telefono_contacto_1']) ?>
          <?= mostrar('tipo_tpv', $servicio['tipo_tpv']) ?>
          <?= mostrar('modelo', $servicio['modelo']) ?>
          <?= mostrar('referencia', $servicio['referencia']) ?>

        </tbody>
      </table>
    </div>

    <div class="mt-4 text-right">
      <a href="/admin/servicios/generar_hs.php?ticket=<?= urlencode($servicio['ticket']) ?>" target="_blank"
         class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
        📄 Descargar Hoja de Servicio
      </a>
    </div>

  </div>
</div>
