<?php
session_start();
require_once '../config.php'; // Incluye tu conexión PDO

$idc = $_SESSION['usuario'] ?? null; // ID técnico logueado

if (!$idc) {
    header('Location: /login.php');
    exit;
}

// --- Procesar formulario ---
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket = trim($_POST['ticket']);
    $monto = trim($_POST['monto']);
    $motivo = trim($_POST['motivo']);

    if ($ticket && $monto && $motivo) {
        $stmt = $pdo->prepare("
            INSERT INTO viaticos (ticket, idc, monto, motivo, estado, fecha_solicitud)
            VALUES (:ticket, :idc, :monto, :motivo, 'Pendiente', NOW())
        ");
        $stmt->execute([
            'ticket' => $ticket,
            'idc' => $idc,
            'monto' => $monto,
            'motivo' => $motivo
        ]);

        $mensaje = "✅ Viático solicitado correctamente.";
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
}

// --- Cargar tickets disponibles ---
$stmt = $pdo->prepare("
    SELECT ticket 
    FROM servicios_omnipos 
    WHERE idc = :idc AND estatus = 'En Ruta'
");
$stmt->execute(['idc' => $idc]);
$tickets = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitar Viáticos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="bg-white shadow p-4 text-center text-xl font-semibold">
  💰 Solicitar Viáticos
</header>

<main class="p-4">
  <?php if (!empty($mensaje)): ?>
    <div class="mb-4 p-3 rounded-xl shadow 
                <?= strpos($mensaje, '✅') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
      <?= htmlspecialchars($mensaje) ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4 bg-white p-4 rounded-2xl shadow">
    <div>
      <label class="block text-gray-700 font-semibold mb-1">Ticket:</label>
      <select name="ticket" class="w-full border rounded-lg p-2" required>
        <option value="">Selecciona un Ticket</option>
        <?php foreach ($tickets as $ticket): ?>
          <option value="<?= htmlspecialchars($ticket) ?>"><?= htmlspecialchars($ticket) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold mb-1">Monto (MXN):</label>
      <input type="number" name="monto" min="1" step="0.01" class="w-full border rounded-lg p-2" required>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold mb-1">Motivo:</label>
      <textarea name="motivo" rows="3" class="w-full border rounded-lg p-2" required></textarea>
    </div>

    <div class="text-center">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl shadow hover:bg-blue-700">
        Solicitar
      </button>
    </div>
  </form>
</main>

</body>
</html>
