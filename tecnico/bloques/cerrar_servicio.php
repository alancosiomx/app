<?php
require_once __DIR__ . '/../../../config.php';
session_start();

$idc = $_SESSION['usuario_nombre'] ?? '';

// Obtener tickets en ruta asignados al técnico
$stmt = $pdo->prepare("SELECT ticket, comercio FROM servicios_omnipos WHERE estatus = 'En Ruta' AND idc = ?");
$stmt->execute([$idc]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ticket_preseleccionado = $_GET['ticket'] ?? '';
?>

<h2 class="text-xl font-bold mb-4">Cerrar Servicio</h2>

<?php if (empty($tickets)): ?>
  <div class="p-4 bg-yellow-100 text-yellow-800 rounded border border-yellow-300">
    No tienes tickets asignados en ruta por el momento. 🕐
  </div>
<?php else: ?>
  <form method="POST" action="cerrar_servicio.php" class="space-y-4">
    <div>
      <label class="block font-semibold">Ticket Asignado:</label>
      <select name="ticket" class="w-full border p-2 rounded" required>
        <option value="">Selecciona un ticket</option>
        <?php foreach ($tickets as $t): ?>
          <option value="<?= htmlspecialchars($t['ticket']) ?>" <?= $t['ticket'] === $ticket_preseleccionado ? 'selected' : '' ?>>
            <?= htmlspecialchars($t['ticket']) ?> - <?= htmlspecialchars($t['comercio']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block font-semibold">Resultado:</label>
      <select name="resultado" class="w-full border p-2 rounded" required>
        <option value="Exito">✅ Éxito</option>
        <option value="Rechazo">❌ Rechazo</option>
      </select>
    </div>

    <div>
      <label class="block font-semibold">Solución:</label>
      <input type="text" name="solucion" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block font-semibold">Solución específica:</label>
      <input type="text" name="solucion_especifica" class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="block font-semibold">Serie instalada:</label>
      <input type="text" name="serie_instalada" class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="block font-semibold">Serie retirada:</label>
      <input type="text" name="serie_retiro" class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="block font-semibold">¿Quién recibió el equipo?</label>
      <input type="text" name="recibio_cliente" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block font-semibold">Comentarios:</label>
      <textarea name="comentarios" class="w-full border p-2 rounded"></textarea>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
      Cerrar Servicio
    </button>
  </form>
<?php endif; ?>
