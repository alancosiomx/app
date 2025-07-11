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
?>

<div id="modalDetalleServicio" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 relative">
    <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl">✖</button>
    <h2 class="text-lg font-bold mb-4 text-gray-800">🔍 Detalle del Servicio - <?= htmlspecialchars(trim($servicio['ticket'])) ?></h2>
    <div class="overflow-y-auto max-h-[70vh] border rounded-md">
      <table class="w-full text-sm text-left text-gray-700 divide-y">
        <?php foreach ($servicio as $campo => $valor): ?>
          <tr class="border-b">
            <th class="bg-gray-100 px-4 py-2 font-semibold w-1/3"><?= ucwords(str_replace("_", " ", $campo)) ?></th>
            <td class="px-4 py-2 whitespace-pre-line"><?= nl2br(htmlspecialchars(trim((string)$valor))) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
