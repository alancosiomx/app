<?php
require_once __DIR__ . '/init.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$mensaje = '';
$ticket = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = trim($_POST['ticket'] ?? '');
    if ($ticket !== '') {
        header('Location: cerrar_servicio.php?ticket=' . urlencode($ticket));
        exit;
    }
    $mensaje = '⚠️ Debes capturar un ticket válido.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar servicio manualmente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
  <div class="max-w-lg mx-auto bg-white rounded shadow p-6 space-y-4">
    <h1 class="text-2xl font-bold">Cerrar servicio manual</h1>
    <?php if ($mensaje): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded text-sm">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
      <label class="block text-sm font-medium">
        Ticket:
        <input type="text" name="ticket" required autocomplete="off"
               class="mt-1 w-full border rounded p-2 text-sm"
               value="<?= htmlspecialchars($ticket) ?>"
               placeholder="Ej. 123456">
      </label>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">
        Continuar
      </button>
    </form>
    <a href="mis_servicios.php" class="text-blue-600 underline text-sm block text-center">← Volver</a>
  </div>
</body>
</html>
