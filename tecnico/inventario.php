<?php
require_once __DIR__ . '/../config.php';
session_start();

$tecnico = $_SESSION['usuario_nombre'] ?? '';
$mensaje = null;

// Filtro de estado
$estado = $_GET['estado'] ?? 'Disponible';

// Obtener equipos asignados al técnico
$stmt = $pdo->prepare("SELECT * FROM inventario_tpv WHERE tecnico_actual = ? AND estado = ? ORDER BY fecha_entrada DESC");
$stmt->execute([$tecnico, $estado]);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejo de solicitud para marcar como DAÑADO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reportar_danio'])) {
    $serie = $_POST['serie'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if ($serie && $motivo) {
        $stmt = $pdo->prepare("INSERT INTO solicitudes_cambio_estado 
            (serie, idc, estado_actual, nuevo_estado, motivo) 
            VALUES (?, ?, 'Disponible', 'Dañado', ?)");
        $stmt->execute([$serie, $tecnico, $motivo]);
        $mensaje = "✅ Solicitud registrada. Será revisada por el administrador.";
    }
}

$contenido = __FILE__;
include __DIR__ . '/layout_tecnico.php';
?>

<div class="p-4">
  <h2 class="text-lg font-bold mb-3">📦 Mi Inventario - Estado: <?= htmlspecialchars($estado) ?></h2>

  <div class="mb-4">
    <a href="?estado=Disponible" class="inline-block bg-blue-600 text-white px-3 py-1 rounded mr-2 <?= $estado === 'Disponible' ? 'font-bold' : '' ?>">Disponible</a>
    <a href="?estado=Dañado" class="inline-block bg-red-600 text-white px-3 py-1 rounded <?= $estado === 'Dañado' ? 'font-bold' : '' ?>">Dañado</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4"><?= $mensaje ?></div>
  <?php endif; ?>

  <?php if (!$equipos): ?>
    <p class="text-sm text-gray-500">No hay equipos en estado <strong><?= htmlspecialchars($estado) ?></strong>.</p>
  <?php else: ?>
    <table class="min-w-full bg-white text-sm rounded shadow">
      <thead class="bg-gray-200 text-gray-700">
        <tr>
          <th class="px-4 py-2 text-left">Serie</th>
          <th class="px-4 py-2 text-left">Banco</th>
          <th class="px-4 py-2 text-left">Estado</th>
          <th class="px-4 py-2 text-left">Observaciones</th>
          <th class="px-4 py-2 text-left">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($equipos as $eq): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?= htmlspecialchars($eq['serie']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($eq['banco']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($eq['estado']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($eq['observaciones']) ?></td>
            <td class="px-4 py-2">
              <?php if ($estado === 'Disponible'): ?>
                <button onclick="abrirModal('<?= $eq['serie'] ?>')" class="text-sm text-red-600 hover:underline">🛠 Reportar daño</button>
              <?php elseif ($estado === 'Dañado'): ?>
                <form method="POST" action="marcar_retorno.php">
                  <input type="hidden" name="serie" value="<?= $eq['serie'] ?>">
                  <button type="submit" class="text-sm text-blue-600 hover:underline">📦 Marcar para retorno</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Modal -->
<div id="modalDanio" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
  <div class="bg-white p-6 rounded max-w-sm w-full">
    <h3 class="text-lg font-bold mb-2">🛠 Reportar equipo como dañado</h3>
    <form method="POST">
      <input type="hidden" name="serie" id="modalSerie">
      <textarea name="motivo" rows="3" required placeholder="Explica el motivo del daño" class="w-full border p-2 rounded mb-4"></textarea>
      <div class="flex justify-between">
        <button type="submit" name="reportar_danio" class="bg-red-600 text-white px-4 py-2 rounded">Enviar</button>
        <button type="button" onclick="cerrarModal()" class="text-gray-600 hover:underline">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
function abrirModal(serie) {
  document.getElementById('modalDanio').classList.remove('hidden');
  document.getElementById('modalDanio').classList.add('flex');
  document.getElementById('modalSerie').value = serie;
}
function cerrarModal() {
  document.getElementById('modalDanio').classList.add('hidden');
  document.getElementById('modalDanio').classList.remove('flex');
}
</script>
