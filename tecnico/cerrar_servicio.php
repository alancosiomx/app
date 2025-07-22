<?php
require_once __DIR__ . '/../init.php';
session_start();

$ticket = $_GET['ticket'] ?? null;

if (!$ticket) {
    echo "<div class='text-red-600 font-bold p-4'>❌ Ticket no proporcionado.</div>";
    exit;
}

// Verifica que el ticket esté en ruta
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio || $servicio['estatus'] !== 'En Ruta') {
    echo "<div class='text-red-600 font-bold p-4'>❌ Este servicio no está disponible para cierre.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar servicio <?= htmlspecialchars($ticket) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
  <div class="max-w-lg mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">✅ Cerrar servicio</h1>
    <p class="mb-4 text-sm text-gray-600">Ticket: <strong><?= htmlspecialchars($ticket) ?></strong></p>

    <form method="POST" action="procesar_cierre.php" class="space-y-4">
      <input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">

      <label class="block text-sm font-medium text-gray-700">
        Nombre de quien atiende (comercio):
        <input type="text" name="atiende" required class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. Luis Ramírez">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Resultado del servicio:
        <select name="resultado" required class="mt-1 w-full border rounded p-2 text-sm">
          <option value="">-- Seleccionar --</option>
          <option value="Éxito">✅ Éxito</option>
          <option value="Rechazo">❌ Rechazo</option>
        </select>
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Serie instalada:
        <input type="text" name="serie_instalada" class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 1234567890">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Serie retirada:
        <input type="text" name="serie_retirada" class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 0987654321">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Comentarios del técnico:
        <textarea name="observaciones" rows="3" class="mt-1 w-full border rounded p-2 text-sm" placeholder="Notas adicionales..."></textarea>
      </label>

      <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">
        Guardar y cerrar servicio
      </button>
    </form>
  </div>
</body>
</html>
