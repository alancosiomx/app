<?php
require_once __DIR__ . '/../../init.php';

$ticket = $_POST['ticket'] ?? $_GET['ticket'] ?? null;

if (!$ticket) {
    echo "<div class='text-red-600 font-bold'>❌ Ticket no proporcionado.</div>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio || $servicio['estatus'] !== 'En Ruta') {
    echo "<div class='text-red-600 font-bold'>❌ Servicio no disponible para cierre.</div>";
    return;
}
?>

<div class="bg-white shadow p-4 rounded max-w-lg mx-auto mt-4">
  <h2 class="text-xl font-bold mb-4">✅ Cerrar servicio</h2>
  <p class="text-sm text-gray-700 mb-4">Ticket: <strong><?= htmlspecialchars($ticket) ?></strong></p>

  <form method="POST" action="/app/tecnico/procesar_cierre.php" class="space-y-4">
    <input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">

    <div>
      <label class="block text-sm font-medium">Nombre de quien atiende (comercio):</label>
      <input type="text" name="atiende" required class="w-full mt-1 p-2 border rounded" placeholder="Ej. Luis Ramírez">
    </div>

    <div>
      <label class="block text-sm font-medium">Resultado del servicio:</label>
      <select name="resultado" required class="w-full mt-1 p-2 border rounded">
        <option value="">-- Seleccionar --</option>
        <option value="Éxito">✅ Éxito</option>
        <option value="Rechazo">❌ Rechazo</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Serie instalada:</label>
      <input type="text" name="serie_instalada" class="w-full mt-1 p-2 border rounded" placeholder="Ej. 123456">
    </div>

    <div>
      <label class="block text-sm font-medium">Serie retirada:</label>
      <input type="text" name="serie_retirada" class="w-full mt-1 p-2 border rounded" placeholder="Ej. 654321">
    </div>

    <div>
      <label class="block text-sm font-medium">Comentarios:</label>
      <textarea name="observaciones" class="w-full mt-1 p-2 border rounded" rows="3" placeholder="Comentarios del servicio..."></textarea>
    </div>

    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">
      Guardar y cerrar servicio
    </button>
  </form>
</div>
