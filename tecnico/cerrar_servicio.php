<?php
require_once __DIR__ . '/init.php';

$ticket = $_POST['ticket'] ?? $_GET['ticket'] ?? null;

if (!$ticket) {
    die("❌ Ticket no proporcionado.");
}

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio || $servicio['estatus'] !== 'En Ruta') {
    die("❌ Este servicio no está disponible para cierre.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar servicio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 max-w-lg mx-auto">
  <h1 class="text-xl font-bold mb-4">✅ Cerrar servicio</h1>
  <p class="mb-2 text-sm text-gray-700">Ticket: <strong><?= htmlspecialchars($ticket) ?></strong></p>

  <form method="POST" action="procesar_cierre.php" class="space-y-4">
    <input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">

    <label class="block text-sm font-medium text-gray-700">
      Nombre de quien atiende (comercio):
      <input type="text" name="atiende" required class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm" placeholder="Ej. Luis Ramírez">
    </label>

    <label class="block text-sm font-medium text-gray-700">
      Resultado del servicio:
      <select name="resultado" required class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm">
        <option value="">-- Seleccionar --</option>
        <option value="Éxito">✅ Éxito</option>
        <option value="Rechazo">❌ Rechazo</option>
      </select>
    </label>

    <label class="block text-sm font-medium text-gray-700">
      Serie instalada:
      <input type="text" name="serie_instalada" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm" placeholder="Ej. 1234567890">
    </label>

    <label class="block text-sm font-medium text-gray-700">
      Serie retirada:
      <input type="text" name="serie_retirada" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm" placeholder="Ej. 9876543210">
    </label>

    <label class="block text-sm font-medium text-gray-700">
      Comentarios adicionales:
      <textarea name="observaciones" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm text-sm" placeholder="Observaciones del técnico..."></textarea>
    </label>

    <button type="submit"
            class="bg-green-600 text-white w-full py-2 rounded font-semibold hover:bg-green-700">
      Guardar y cerrar servicio
    </button>
  </form>
</body>
</html>
